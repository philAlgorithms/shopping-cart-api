<?php

namespace Database\Seeders;

use App\Models\Media\Mime;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MimeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $mimes = [
            [
                'extension'=>'.aac',
                'type'=>'audio/aac',
                'document'=>'AAC audio'
            ],
            [
                'extension'=>'.abw',
                'type'=>'application/x-abiword',
                'document'=>'AbiWord document'
            ],
            [
                'extension'=>'.arc',
                'type'=>'application/x=freearc',
                'document'=>'Archive document (muktiple files embedded)'
            ],
            [
                'extension'=>'.avif',
                'type'=>'image/avif',
                'document'=>'AVIF image'
            ],
            [
                'extension'=>'.avi',
                'type'=>'video/x-msvideo',
                'document'=>'AVI: Audio Video Interleave'
            ],
            [
                'extension'=>'azw',
                'type'=>'application/vnd.amazon.ebook',
                'document'=>'Amazon Kindle eBook format'
            ],
            [
                'extension'=>'.bin',
                'type'=>'application/octet-stream',
                'document'=>'Any kind of binary data'
            ],
            [
                'extension'=>'.bmp',
                'type'=>'image/bmp',
                'document'=>'Windows OS/2 Bitmap Graphics'
            ],
            [
                'extension'=>'.bz',
                'type'=>'application/x-bzip',
                'document'=>'BZip archive'
            ],
            [
                'extension'=>'.bz2',
                'type'=>'application/x-bzip2',
                'document'=>'BZip2 archive'
            ],
            [
                'extension'=>'cda',
                'type'=>'application/x-cdf',
                'document'=>'CD audio'
            ],
            [
                'extension'=>'.csh',
                'type'=>'application/x-csh',
                'document'=>'C-Schell script'
            ],
            [
                'extension'=>'.css',
                'type'=>'text/css',
                'document'=>'Cascading Style Sheets (CSS)'
            ],
            [
                'extension'=>'.csv',
                'type'=>'text/csv',
                'document'=>'Comma-separated values (CSV)'
            ],
            [
                'extension'=>'.doc',
                'type'=>'application/msword',
                'document'=>'Microsoft Word'
            ],
            [
                'extension'=>'.docx',
                'type'=>'application/vnd.openxmlformarts-officedocuments.wordprocessingml.document',
                'document'=>'Microsoft Word (OpenXml)'
            ],
            [
                'extension'=>'.eot',
                'type'=>'application/vnd.ms-fontobject',
                'document'=>'MS Embedded OpenType fonts'
            ],
            [
                'extension'=>'.epub',
                'type'=>'application/epub+zip',
                'document'=>'Electronic publication (EPUB)'
            ],
            [
                'extension'=>'.gz',
                'type'=>'application/gzip',
                'document'=>'GZip Compressed Archive'
            ],
            [
                'extension'=>'.gif',
                'type'=>'image/gif',
                'document'=>'Graphics Interchange Format (GIF)'
            ],
            [
                'extension'=>'.htm',
                'type'=>'text/html',
                'document'=>'HyperTextMarkupLanguage (HTML)'
            ],
            [
                'extension'=>'.html',
                'type'=>'text/html',
                'document'=>'HyperTextMarkupLanguage (HTML)'
            ],
            [
                'extension'=>'.ico',
                'type'=>'image/vnd.microsoft.icon',
                'document'=>'Icon format'
            ],
            [
                'extension'=>'.ics',
                'type'=>'text/calendar',
                'document'=>'iCalendar format'
            ],
            [
                'extension'=>'.jar',
                'type'=>'application/java-archive',
                'document'=>'Java Archive (JAR)'
            ],
            [
                'extension'=>'.jpeg',
                'type'=>'image/jpeg',
                'document'=>'JPEG images'
            ],
            [
                'extension'=>'.jpg',
                'type'=>'image/jpeg',
                'document'=>'JPEG images'
            ],
            [
                'extension'=>'.js',
                'type'=>'text/javascript',
                'document'=>'Javascript'
            ],
            [
                'extension'=>'.json',
                'type'=>'application/json',
                'document'=>'JSON format'
            ],
            [
                'extension'=>'.jsonld',
                'type'=>'application/ld+json',
                'document'=>'JSON-LD format'
            ],
            [
                'extension'=>'.mid',
                'type'=>'audio/midi',
                'document'=>'Musical Instrument Digital Interface (MIDI)'
            ],
            [
                'extension'=>'.midi',
                'type'=>'audio/x-midi',
                'document'=>'Musical Instrument Digital Interface (MIDI)'
            ],
            [
                'extension'=>'.mjs',
                'type'=>'text/javascript',
                'document'=>'Javascript module'
            ],
            [
                'extension'=>'.mp3',
                'type'=>'audio/mpeg',
                'document'=>'MP3 audio'
            ],
            [
                'extension'=>'.mp4',
                'type'=>'video/mp4',
                'document'=>'MP4 video'
            ],
            [
                'extension'=>'.mpeg',
                'type'=>'video/mpeg',
                'document'=>'MPEG video'
            ],
            [
                'extension'=>'.mpkg',
                'type'=>'application/vnd.apple.installer+xml',
                'document'=>'Apple Installer Package'
            ],
            [
                'extension'=>'.odp',
                'type'=>'application/vnd.oasis.opendocument.presentation',
                'document'=>'OpenDocument presentation document'
            ],
            [
                'extension'=>'.ods',
                'type'=>'application/vnd.oasis.opendocument.spreadsheet',
                'document'=>'OpenDocument spredsheet document'
            ],
            [
                'extension'=>'.odt',
                'type'=>'application/vnd.oasis.opendocument.text',
                'document'=>'OpenDocument text document'
            ],
            [
                'extension'=>'.oga',
                'type'=>'audio/ogg',
                'document'=>'OGG audio'
            ],
            [
                'extension'=>'.ogv',
                'type'=>'video/ogg',
                'document'=>'OGG video'
            ],
            [
                'extension'=>'.ogx',
                'type'=>'application/ogg',
                'document'=>'OGG'
            ],
            [
                'extension'=>'.opus',
                'type'=>'audio/opus',
                'document'=>'Opus audio'
            ],
            [
                'extension'=>'.otf',
                'type'=>'font/otf',
                'document'=>'OpenType font'
            ],
            [
                'extension'=>'.png',
                'type'=>'image/png',
                'document'=>'Portable Network Graphics'
            ],
            [
                'extension'=>'.pdf',
                'type'=>'application/pdf',
                'document'=>'Adobe Portable Document Format (PDF)'
            ],
            [
                'extension'=>'.php',
                'type'=>'application/x-httpd-php',
                'document'=>'HyperText Preprocessor (Personal Home Page)'
            ],
            [
                'extension'=>'.ppt',
                'type'=>'application/vnd.ms-powerpoint',
                'document'=>'Microsoft PowerPoint'
            ],
            [
                'extension'=>'.pptx',
                'type'=>'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                'document'=>'Microsoft PowerPoint (OpenXML)'
            ],
            [
                'extension'=>'.rar',
                'type'=>'application/vnd.rar',
                'document'=>'RAR archive'
            ],
            [
                'extension'=>'.rtf',
                'type'=>'application/rtf',
                'document'=>'Rich Text Format (RTF)'
            ],
            [
                'extension'=>'.sh',
                'type'=>'application/x-sh',
                'document'=>'Bourne shell script'
            ],
            [
                'extension'=>'.svg',
                'type'=>'image/svg+xml',
                'document'=>'Scalable Vector Graphics (SVG)'
            ],
            [
                'extension'=>'.swf',
                'type'=>'application/x-shockwave-flash',
                'document'=>'Adobe Flash'
            ],
            [
                'extension'=>'.tar',
                'type'=>'application/x-tar',
                'document'=>'Tape Archive (TAR)'
            ],
            [
                'extension'=>'.tif',
                'type'=>'image/tiff',
                'document'=>'Tagged Image File Format (TIFF)'
            ],
            [
                'extension'=>'.tiff',
                'type'=>'image/tiff',
                'document'=>'Tagged Image File Format (TIFF)'
            ],
            [
                'extension'=>'.ts',
                'type'=>'video/mp2t',
                'document'=>'MPEG transport stream'
            ],
            [
                'extension'=>'.ttf',
                'type'=>'font/ttf',
                'document'=>'TrueType Font'
            ],
            [
                'extension'=>'.txt',
                'type'=>'text/plain',
                'document'=>'Text'
            ],
            [
                'extension'=>'.vsd',
                'type'=>'application/vnd.visio',
                'document'=>'Microsoft Visio'
            ],
            [
                'extension'=>'.wav',
                'type'=>'audio/wav',
                'document'=>'Waveform Audio Format'
            ],
            [
                'extension'=>'.weba',
                'type'=>'audio/webm',
                'document'=>'WEBM audio'
            ],
            [
                'extension'=>'.webm',
                'type'=>'video/webm',
                'document'=>'WEBM video'
            ],
            [
                'extension'=>'.webp',
                'type'=>'image/webp',
                'document'=>'WEBP image'
            ],
            [
                'extension'=>'.woff',
                'type'=>'font/woff',
                'document'=>'Web Open Font Format (WOFF)'
            ],
            [
                'extension'=>'.woff2',
                'type'=>'font/woff2',
                'document'=>'Web Open Font Format (WOFF)'
            ],
            [
                'extension'=>'.xhtml',
                'type'=>'application/xhtml+xml',
                'document'=>'XHTML'
            ],
            [
                'extension'=>'.xls',
                'type'=>'application/vnd.ms-excel',
                'document'=>'Microsoft Excel'
            ],
            [
                'extension'=>'.xlsx',
                'type'=>'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'document'=>'Microsoft Excel (OpenXML)'
            ],
            [
                'extension'=>'.xml',
                'type'=>'application.xml',
                'document'=>'XML'
            ],
            [
                'extension'=>'.xul',
                'type'=>'application/vnd.mozilla.xul+xml',
                'document'=>'XUL'
            ],
            [
                'extension'=>'.zip',
                'type'=>'application/zip',
                'document'=>'ZIP archive'
            ],
            [
                'extension'=>'.3gp',
                'type'=>'video/3gpp',
                'document'=>'3GPP audio/video container'
            ],
            [
                'extension'=>'.3g2',
                'type'=>'video/3gpp2',
                'document'=>'3GPP2 audio/video container'
            ],
            [
                'extension'=>'.7z',
                'type'=>'application/x-7z-compressed',
                'document'=>'7-zip archive'
            ],
        ];

        foreach($mimes as $mime)
        {
            Mime::create($mime);
        }
    }
}
