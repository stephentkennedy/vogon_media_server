<?php

namespace dlna;

class media {
    protected $item_definitions = [
        'video' => 'object.item.videoItem',
        'audio' => 'object.item.audioItem',
        'image' => 'object.item.imageItem'
    ];

    protected $allowed_formats = [
        'audio' => [
            'audio/mpeg',
            'application/octet-stream', //badly formed mp3s will sometimes contiin this mime_type
            'audio/aac',
            'audio/flac',
            'audio/x-flac' //Non-standard, may not work with all sinks
        ],
        'video' => [
            'video/mp4',
            'video/mpeg',
            'video/x-matroska'
        ],
        'image' => [
            'image/jpeg',
            'image/png',
            'image/gif'
        ]
    ];

    protected $meta_defaults = [
        'creator' => '',
        'genre' => '',
        'artist' => '',
        'album' => '',
        'track' => '',
        'director' => '',
        'date' => '',
        'description' => '',
        'longDescription' => '',
        'language' => '',
        'captions' => '',
    ];

    protected static $dh = null; //The datahandler we'll use if needed.

    public $format, $mime, $meta, $icon, $filesize, $duration, $bitrate, $resolution, $name, $filename, $item_definition;

    public function __construct($media_array){
        if(empty($media_array['data_id']) || !is_numeric($media_array['data_id'])){
            $this->mime = false;
            return;
        }


        //Prepare our meta data.
        $this->meta = $this->meta_defaults;

        if(!empty($media_array['poster'])){
            $this->icon = \dir2url($media_array['poster']);
        }

        if(!empty($media_array['artist'])){
            $this->meta['artist'] = $media_array['artist'];
        }

        if(!empty($media_array['director'])){
            $this->meta['director'] = $media_array['director'];
        }

        if(!empty($media_array['desc'])){
            $this->meta['description'] = $media_array['desc'];
            $this->meta['longDescription'] = $media_array['desc'];
        }

        if(!empty($media_array['track'])){
            $this->meta['track'] = $media_array['track'];
        }

        if(!empty($media_array['subtitles'])){
            $this->meta['captions'] = \dir2url($media_array['subtitles']);
        }

        if(!empty($media_array['starring'])){
            $this->meta['actor'] = $media_array['starring'];
        }

        if(!empty($media_array['year'])){
            $date = \DateTime::createFromFormat('Y', $media_array['year']);
            $date_string = $date ? $date->format('Y-m-d') : '';
            $this->meta['date'] = $date_string;
        }

        if(!empty($media_array['release'])){
            $date = \DateTime::createFromFormat('Y', $media_array['release']);
            $date_string = $date ? $date->format('Y-m-d') : '';
            $this->meta['date'] = $date_string;
        }

        $this->identify_format($media_array);

        if($this->mime === false){
            return;
        }

        $this->name = $media_array['data_name'];

        if(empty($media_array['filesize'])){

        }
    }

    private function identify_format($media_array){
        if(!empty($media_array['mime_type'])){
            $this->mime = $media_array['mime_type'];
            $filename = $media_array['data_content'];
            if(!file_exists($filename)){
                $filename = \url2dir($filename);
            }
            $this->filename = $filename;
            return;
        }

        $filename = $media_array['data_content'];
        $type = $media_array['data_type'];

        switch($type){
            case 'video':
            case 'tv':
                $base_type = 'video';
                break;
            case 'audio':
                $base_type = 'audio';
                break;
            default:
                $this->mime = false; //We can't serve the file so mark it as false.
                return;
                break;
        }

        $file_exists = file_exists($filename);

        if(!$file_exists){
            $backup_file_name = \url2dir($filename);
            $file_exists = file_exists($backup_file_name);
            if(!$file_exists){
                $this->mime = false;
                return;
            }
            $filename = $backup_file_name;
        }

        $mime_type = mime_content_type($filename);
        $mime_type_array = explode('/', $mime_type);

        if(
            $base_type != $mime_type_array[0]
            && $mime_type != 'application/octet-stream'
        ){
            $this->mime = false;
            return;
        }

        $allowed_mimetypes = $this->allowed_formats[$base_type];

        if(!in_array($mime_type, $allowed_mimetypes)){
            $this->mime = false;
            return;
        }

        $this->mime = $mime_type;
        $this->filename = $filename;

        $dh = self::get_handler();
        $id = $media_array['data_id'];
        $data_meta = $dh->child;

        $data_meta->addRecord([
            'data_id' => $id,
            'data_meta_name' => 'mime_type',
            'data_meta_content' => $mime_type
        ]);

        if($filename != $media_array['data_content']){
            $data = $dh->root;
            $update = [
                'data_content' => $filename
            ];
            $data->updateRecord($update, $id);
        }

        if(empty($media_array['filesize'])){
            $filesize = filesize($filename);
            $data_meta->addRecord([
                'data_id' => $id,
                'data_meta_name' => 'filesize',
                'data_meta_content' => $filesize
            ]);
            $this->filesize = $filesize;
        }else{
            $this->filesize = $media_array['filesize'];
        }

        $this->item_definition = $this->item_definitions[$base_type];
    }

    public static function get_handler(){
        if(self::$dh == null){
            $data_table = new \db_handler('data');
            $data_meta_table = new \db_handler('data_meta');
            $dwm = $data_table->meta_link($data_meta_table, [
                'meta_key_field' => 'data_meta_name',
                'meta_value_field' => 'data_meta_content'
            ]);
            self::$dh = $dwm;
        }

        return self::$dh;
    }
};