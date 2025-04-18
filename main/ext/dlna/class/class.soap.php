<?php

namespace dlna;

class soap {
    const ENV_NS = 'http://schemas.xmlsoap.org/soap/envelope/';

    public static function parse(): array {
        $xml = file_get_contents('php://input');
        $dom = new \DOMDocument();
        if (!$xml || !@$dom->loadXML($xml)) {
            return ['action' => '', 'args' => []];
        }
        $body = $dom->getElementsByTagNameNS(self::ENV_NS, 'Body')->item(0);
        if (!$body) {
            return ['action' => '', 'args' => []];
        }
        $action_el = null;
        foreach ($body->childNodes as $child) {
            if ($child->nodeType === XML_ELEMENT_NODE) {
                $action_el = $child;
                break;
            }
        }
        if (!$action_el) {
            return ['action' => '', 'args' => []];
        }
        $args = [];
        foreach ($action_el->childNodes as $child) {
            if ($child->nodeType === XML_ELEMENT_NODE) {
                $args[$child->localName] = $child->textContent;
            }
        }
        return ['action' => $action_el->localName, 'args' => $args];
    }

    public static function response(string $service_ns, string $action, array $body_params): string {
        $dom = new \DOMDocument('1.0', 'utf-8');
        $envelope = $dom->createElementNS(self::ENV_NS, 's:Envelope');
        $envelope->setAttribute('s:encodingStyle', 'http://schemas.xmlsoap.org/soap/encoding/');
        $dom->appendChild($envelope);

        $body = $dom->createElementNS(self::ENV_NS, 's:Body');
        $envelope->appendChild($body);

        $response = $dom->createElementNS($service_ns, 'u:' . $action . 'Response');
        $body->appendChild($response);

        foreach ($body_params as $name => $value) {
            $el = $dom->createElement($name);
            $el->appendChild($dom->createTextNode($value));
            $response->appendChild($el);
        }

        return $dom->saveXML();
    }

    public static function fault(string $code, string $description): string {
        $dom = new \DOMDocument('1.0', 'utf-8');
        $envelope = $dom->createElementNS(self::ENV_NS, 's:Envelope');
        $envelope->setAttribute('s:encodingStyle', 'http://schemas.xmlsoap.org/soap/encoding/');
        $dom->appendChild($envelope);

        $body = $dom->createElementNS(self::ENV_NS, 's:Body');
        $envelope->appendChild($body);

        $fault = $dom->createElementNS(self::ENV_NS, 's:Fault');
        $body->appendChild($fault);

        $fc = $dom->createElement('faultcode');
        $fc->appendChild($dom->createTextNode($code));
        $fault->appendChild($fc);

        $fs = $dom->createElement('faultstring');
        $fs->appendChild($dom->createTextNode($description));
        $fault->appendChild($fs);

        return $dom->saveXML();
    }
}
