<?php

namespace dlna;

/**
 * This class is the main xml builder for DNLA server communications.
 * The entire protocol is a SOAP based XML api with UDP broadcasting.
 */

class xml_base {
    public function addNodeWithText($parent, $tag, $text){
        $ndTag = $parent->ownerDocument->createElement($tag);
        $ndTag_text = $parent->ownerDocument->createTextNode((mb_detect_encoding($text,'auto')=='UTF-8')?$text:utf8_encode($text));
        $ndTag->appendChild($ndTag_text);
        $parent->appendChild($ndTag);
        return $ndTag;
    }
}

class xml_builder extends xml_base {
   
    protected   $dom, //Our DOMDocument
                $root; //Our Root Namespace within $dom

    public $parent_id = null;
    public $count = 0;

    public function __construct($parent_id){
        $this->parent_id = $parent_id; //We don't know the type of this yes, should investigate so we can cast;

        //Prepare our document constants
        $this->dom = new \DOMDocument('1.0', 'utf-8');
        $this->dom->formatOutput = true;
        $this->root = $this->dom->createElementNS('urn:schemas-upnp-org:metadata-1-0/DIDL-Lite/', 'DIDL-Lite');
        $this->root->setAttribute('xmlns:dc', 'http://purl.org/dc/elements/1.1/');
        $this->root->setAttribute('xmlns:upnp', 'urn:schemas-upnp-org:metadata-1-0/upnp/');
        $this->root->setAttribute('xmlns:dlna', 'urn:schemas-dlna-org:metadata-1-0/');

        $this->dom->appendChild($this->root);
    }

    public function addFolder($title, $id){
        $node = $this->dom->createElement('container');
        $node->setAttribute('id', $id);
        $node->setAttribute('parentID', $this->parent_id);
        $node->setAttribute('restricted', '1');
        $this->addNodeWithText($node, 'dc:title', $title);
        $this->addNodeWithText($node, 'upnp:class', 'object.container.storageFolder');
        $this->root->appendChild($node);
        $this->count++;
        return new xml_node($node, 'dir');
    }

    public function addItem($media_item, $id){
        $node = $this->dom->createElement('item');
        $node->setAttribute('id', $id);
        $node->setAttribute('parentID', $this->parent_id);
        $node->setAttribute('restricted', '1');
        $this->addNodeWithText($node, 'dc:title', $media_item->name);
        $this->root->appendChild($node);
        $this->count++;
        $this->addNodeWithText($node, 'upnp:class', $media_item->item_definition);
        return new xml_node($node, 'media', $media_item);
    }

    public function slice($start_id, $count = -1){
        $i = $this->root->firstChild;
        while($start_id > 0 && $this->count > 0){
            $start_id--;
            $c = $i;
            $i = $i->nextSibling;
            $this->root->removeChild($c);
            $this->count--;
        }
        if($count < 0 || $this->count < $count){
            return $this;
        }
        $this->count = $count;
        while($count > 0 && $i !== null){
            $count--;
            $i = $i->nextSibling;
        }
        while($i !== null){
            $c = $i;
            $i = $i->nextSibling;
            $this->root->removeChild($c);
        }
        return $this;
    }

    public function getXml($no_header = true){
        return $this->dom->saveXml($no_header ? $this->dom->firstChild : null);
    }
    
}

class xml_node extends xml_base{
    protected $node = null;
    protected $type = 'dir';
    protected $allowed_types = [
        'media',
        'dir'
    ];
    protected $media;

    public function __construct($node, $type='dir', $media_item = false){
        if(!in_array($type, $this->allowed_types)){
            return false;
        }
        $this->node = $node;
        $this->type = $type;

        if(
            $this->type == 'media'
            && $media_item !== false
            && $media_item instanceof media
        ){
            $this->media = $media_item;
        }
    }

    public function addResource($url, $mime, $filesize = null, $duration_seconds = null) {
        $res = $this->node->ownerDocument->createElement('res');
        $res->setAttribute('protocolInfo', 'http-get:*:' . $mime . ':*');
        if ($filesize !== null) {
            $res->setAttribute('size', (string)$filesize);
        }
        if ($duration_seconds !== null) {
            $h = floor($duration_seconds / 3600);
            $m = floor(($duration_seconds % 3600) / 60);
            $s = $duration_seconds % 60;
            $res->setAttribute('duration', sprintf('%d:%02d:%02d', $h, $m, $s));
        }
        $text = $this->node->ownerDocument->createTextNode($url);
        $res->appendChild($text);
        $this->node->appendChild($res);
    }

    public function addDescription($text) {
        $this->addNodeWithText($this->node, 'dc:description', $text);
    }

    public function addAlbumArtUri($url) {
        $node = $this->node->ownerDocument->createElement('upnp:albumArtURI');
        $node->setAttribute('xmlns:dlna', 'urn:schemas-dlna-org:metadata-1-0/');
        $node->setAttribute('dlna:profileID', 'JPEG_TN');
        $text = $this->node->ownerDocument->createTextNode($url);
        $node->appendChild($text);
        $this->node->appendChild($node);
    }

}

class root_xml_builder extends xml_base{
    protected $defaults = [
        'friendly_name' => 'Vogon UPnP Server',
        'bound_ip' => '127.0.0.1',
        'serialNumber' => '12345678',
        'uuid' => 'uuid:17f4370c-dae1-11e3-841f-00e04c6a3575',
        'routes' => [
            'content_directory' => [
                'control' => '/dir/control',
                'event' => '/dir/event'
            ],
            'connection_manager' => [
                'control' => '/con/control',
                'event' => '/con/event',
            ]
        ],
        'uri_base' => '/dlna'
    ];

    protected $settings = [];

    public function __construct($options = []){
        $this->settings = array_merge($this->defaults, $options);
    }

    public function output(){
        $dom = new \DOMDocument('1.0', 'utf-8');
        $root = $dom->createElementNS('urn:schemas-upnp-org:device-1-0', 'root');
        $root->setAttribute('xmlns:dlna', 'urn:schemas-dlna-org:device-1-0');
        $dom->appendChild($root);

        //Spec Version
        $specVersion = $dom->createElement('specVersion');
        $this->addNodeWithText($specVersion, 'major', '1');
        $this->addNodeWithText($specVersion, 'minor', '0');
        $root->appendChild($specVersion);

        //Device Node
        $device = $dom->createElement('device');
        $root->appendChild($device);
        $this->addNodeWithText($device, 'deviceType', 'urn:schemas-upnp-org:device:MediaServer:1');
        $this->addNodeWithText($device, 'friendlyName', $this->settings['friendly_name']);
        $this->addNodeWithText($device, 'manufacturer', 'Steph Kennedy');
        $this->addNodeWithText($device, 'manufacturerURL', 'https://www.stephkennedy.dev');
        $this->addNodeWithText($device, 'modelDescription', 'A PHP Media Server');
        $this->addNodeWithText($device, 'modelName', 'Vogon UPnP Server');
        $this->addNodeWithText($device, 'modelNumber', \VER); //Vogon Server Version Number
        $this->addNodeWithText($device, 'modelURL', 'https://www.stephkennedy.dev');
        $this->addNodeWithText($device, 'serialNumber', $this->settings['serialNumber']);
        $this->addNodeWithText($device, 'UDN', $this->settings['uuid']);
        $this->addNodeWithText($device, 'dlna:X_DLNADOC', 'DMS-1.50');

        //Icon
        $iconList = $dom->createElement('iconList');
        $device->appendChild($iconList);
        $icon = $dom->createElement('icon');
        $iconList->appendChild($icon);
        $this->addNodeWithText($icon, 'mimetype', 'image/png');
        $this->addNodeWithText($icon, 'width', '342'); //pixels without unit
        $this->addNodeWithText($icon, 'height', '330'); //"
        $this->addNodeWithText($icon, 'depth', '24'); //Color depth in bits without unit?
        $this->addNodeWithText($icon, 'url', '/icon');

        //URL Base — built early so service URLs can use it as a prefix
        $base_url = 'http://' . $this->settings['bound_ip'] . \build_slug('/dlna');

        //Services
        $serviceList = $dom->createElement('serviceList');
        $device->appendChild($serviceList);
        $content_directory = $dom->createElement('service');
        $connection_manager = $dom->createElement('service');
        $serviceList->appendChild($content_directory);
        $serviceList->appendChild($connection_manager);

        //Content Directory
        $this->addNodeWithText($content_directory, 'serviceType', 'urn:schemas-upnp-org:service:ContentDirectory:1');
        $this->addNodeWithText($content_directory, 'serviceId', 'urn:upnp-org:serviceId:ContentDirectory');
        $this->addNodeWithText($content_directory, 'controlURL', $base_url . $this->settings['routes']['content_directory']['control']);
        $this->addNodeWithText($content_directory, 'eventSubURL', $base_url . $this->settings['routes']['content_directory']['event']);
        $this->addNodeWithText($content_directory, 'SCPDURL', $base_url . '/cdScpd.xml');

        //Connection Manager
        $this->addNodeWithText($connection_manager, 'serviceType', 'urn:schemas-upnp-org:service:ConnectionManager:1');
        $this->addNodeWithText($connection_manager, 'serviceId', 'urn:upnp-org:serviceId:ConnectionManager');
        $this->addNodeWithText($connection_manager, 'controlURL', $base_url . $this->settings['routes']['connection_manager']['control']);
        $this->addNodeWithText($connection_manager, 'eventSubURL', $base_url . $this->settings['routes']['connection_manager']['event']);
        $this->addNodeWithText($connection_manager, 'SCPDURL', $base_url . '/cmScpd.xml');

        $this->addNodeWithText($root, 'URLBase', $base_url);

        return $dom->saveXML();
    }
}