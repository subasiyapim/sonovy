<?php

namespace App\Services;

use App\Enums\ProductTypeEnum;
use App\Models\Product;
use DOMDocument;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DDEXService
{
    public static function makeAudioXml(Product $product)
    {
        $xml = new DOMDocument("1.0", "UTF-8");
        $xml->formatOutput = true;

        // Root element: NewReleaseMessage
        $newReleaseMessage = $xml->createElement("ern:NewReleaseMessage");
        $newReleaseMessage->setAttribute("xmlns:ern", "http://ddex.net/xml/ern/43");
        $newReleaseMessage->setAttribute("xmlns:xsi", "http://www.w3.org/2001/XMLSchema-instance");
        $newReleaseMessage->setAttribute("xsi:schemaLocation",
            "http://ddex.net/xml/ern/43 http://ddex.net/xml/ern/43/release-notification.xsd");
        $newReleaseMessage->setAttribute("ReleaseProfileVersionId", "Audio");
        $newReleaseMessage->setAttribute("LanguageAndScriptCode", "en");
        $newReleaseMessage->setAttribute("AvsVersionId", "3");
        $xml->appendChild($newReleaseMessage);

        // MessageHeader
        $messageHeader = $xml->createElement("MessageHeader");
        $newReleaseMessage->appendChild($messageHeader);

        $messageThreadId = $xml->createElement("MessageThreadId", $product->id);
        $messageHeader->appendChild($messageThreadId);

        $messageId = $xml->createElement("MessageId", $product->id.".1");
        $messageHeader->appendChild($messageId);

        $messageSender = $xml->createElement("MessageSender");
        $messageHeader->appendChild($messageSender);

        $partyId = $xml->createElement("PartyId", "PADPIDA2013042401U");
        $messageSender->appendChild($partyId);

        $partyName = $xml->createElement("PartyName");
        $messageSender->appendChild($partyName);
        $fullName = $xml->createElement("FullName", "UniversalMusicGroup");
        $partyName->appendChild($fullName);

        $messageRecipient = $xml->createElement("MessageRecipient");
        $messageHeader->appendChild($messageRecipient);
        $recipientPartyId = $xml->createElement("PartyId", "PADPIDA2009101501Y");
        $messageRecipient->appendChild($recipientPartyId);
        $recipientPartyName = $xml->createElement("PartyName");
        $messageRecipient->appendChild($recipientPartyName);
        $recipientFullName = $xml->createElement("FullName", "Sony DADC");
        $recipientPartyName->appendChild($recipientFullName);

        $messageCreatedDateTime = $xml->createElement("MessageCreatedDateTime", now()->toIso8601String());
        $messageHeader->appendChild($messageCreatedDateTime);

        // PartyList
        $partyList = $xml->createElement("PartyList");
        $newReleaseMessage->appendChild($partyList);

        if ($product->artists()->count() > 0) {
            foreach ($product->artists as $artist) {
                $party = $xml->createElement("Party");
                $partyList->appendChild($party);

                $partyReference = $xml->createElement("PartyReference", "P".$artist->id);
                $party->appendChild($partyReference);

                $partyName = $xml->createElement("PartyName");
                $party->appendChild($partyName);

                $fullName = $xml->createElement("FullName", $artist->name);
                $partyName->appendChild($fullName);

                if ($artist->country) {
                    $party->appendChild($xml->createElement("CountryCode", $artist->country->code));
                }
            }
        }

        // ResourceList
        $resourceList = $xml->createElement("ResourceList");
        $newReleaseMessage->appendChild($resourceList);

        if ($product->songs()->count() > 0) {
            foreach ($product->songs as $song) {
                $soundRecording = $xml->createElement("SoundRecording");
                $resourceList->appendChild($soundRecording);

                $resourceReference = $xml->createElement("ResourceReference", "A".$song->id);
                $soundRecording->appendChild($resourceReference);

                $type = $xml->createElement("Type", "MusicalWorkSoundRecording");
                $soundRecording->appendChild($type);

                $soundRecordingEdition = $xml->createElement("SoundRecordingEdition");
                $soundRecording->appendChild($soundRecordingEdition);

                $resourceId = $xml->createElement("ResourceId");
                $soundRecordingEdition->appendChild($resourceId);

                $isrc = $xml->createElement("ISRC", $song->isrc);
                $resourceId->appendChild($isrc);

                $pLine = $xml->createElement("PLine");
                $soundRecordingEdition->appendChild($pLine);

                $year = $xml->createElement("Year", date('Y', strtotime($product->release_date)));
                $pLine->appendChild($year);

                $pLineText = $xml->createElement("PLineText", $product->p_line);
                $pLine->appendChild($pLineText);

                $technicalDetails = $xml->createElement("TechnicalDetails");
                $soundRecordingEdition->appendChild($technicalDetails);

                $technicalResourceDetailsReference = $xml->createElement("TechnicalResourceDetailsReference",
                    "T".$song->id);
                $technicalDetails->appendChild($technicalResourceDetailsReference);

                $deliveryFile = $xml->createElement("DeliveryFile");
                $technicalDetails->appendChild($deliveryFile);

                $type = $xml->createElement("Type", "AudioFile");
                $deliveryFile->appendChild($type);

                $file = $xml->createElement("File");
                $deliveryFile->appendChild($file);

                $uri = $xml->createElement("URI", $song->path);
                $file->appendChild($uri);
            }
        }

        // ReleaseList
        $releaseList = $xml->createElement("ReleaseList");
        $newReleaseMessage->appendChild($releaseList);

        $release = $xml->createElement("Release");
        $releaseList->appendChild($release);

        $releaseReference = $xml->createElement("ReleaseReference", "R".$product->id);
        $release->appendChild($releaseReference);

        $releaseType = $xml->createElement("ReleaseType", "Album");
        $release->appendChild($releaseType);

        $releaseId = $xml->createElement("ReleaseId");
        $release->appendChild($releaseId);

        $icpn = $xml->createElement("ICPN", $product->upc_code);
        $releaseId->appendChild($icpn);

        $displayTitleText = $xml->createElement("DisplayTitleText", $product->name);
        $release->appendChild($displayTitleText);

        $displayTitle = $xml->createElement("DisplayTitle");
        $displayTitle->setAttribute("ApplicableTerritoryCode", "Worldwide");
        $displayTitle->setAttribute("IsDefault", "true");
        $release->appendChild($displayTitle);

        $titleText = $xml->createElement("TitleText", $product->name);
        $displayTitle->appendChild($titleText);

        // Adım 2: XML Kaydetme İşlemi
        $xmlContent = $xml->saveXML();

        // Adım 3: Depolama Yolu ve İzinler
        $xmlFilePath = Storage::disk('xml_files')->path($product->id."_ddex_audio_release.xml");

        // Dosya yolunun doğru olduğundan ve yazma izinlerinin uygun olduğundan emin olun
        if (!is_dir(dirname($xmlFilePath))) {
            mkdir(dirname($xmlFilePath), 0755, true);
        }

        // Dosyayı UTF-8 BOM ile kaydedelim
        file_put_contents($xmlFilePath, "\xEF\xBB\xBF".$xmlContent);

        // Adım 4: UTF-8 Kodlaması
        $file_name = $product->id."_ddex_audio_release";

        $product->update(['xml_path' => $file_name]);

        echo "XML file has been generated successfully.";

        // Ek Adım: XML'i Bellekte Doğrulama
        if ($xml->schemaValidate("path/to/ddex.xsd")) {
            echo "XML is valid.";
        } else {
            echo "XML is not valid.";
        }
    }


    public static function makeVideoXml(Product $product)
    {
        //
    }

    public static function makeRingToneXml(Product $product)
    {
        //
    }
}
