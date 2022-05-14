<?php
/*
 * Author: Vương Quốc Huy
 * Email: vqh.programming@gmail.com
 * Website: https://www.vqhteam.com
*/
namespace Vqhteam\Ytdownload;
class YTDownload {
    public static function getLink($video_id)
    {
        if (empty($video_id)){
            throw new \Exception("Youtube Video id is empty");
        }
        $url = "https://www.youtube.com/watch?v=".$video_id;
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $headers = array(
            "user-agent: Mozilla/5.0 (Linux; U; Android 4.4.2; en-us; SCH-I535 Build/KOT49H) AppleWebKit/534.30 (KHTML, like Gecko) Version/4.0 Mobile Safari/534.30",
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        $resp = curl_exec($curl);
        if (curl_errno($curl)){
            return [];
        }
        curl_close($curl);
        if (empty($resp)){
            return [];
        }
        $ex = explode('var ytInitialPlayerResponse = {',$resp);
        if (!isset($ex[1])){
            return [];
        }
        $data = explode(';</script>',$ex[1]);
        if (!isset($data[0])){
            return [];
        }
        $json = json_decode("{".$data[0],true);
        if (!$json){
            return [];
        }
        $videos = [];
        $i=0;
        foreach ($json["streamingData"]["adaptiveFormats"] as $video)
        {
            $videos[$i]["itag"]=$video["itag"];
            $videos[$i]["link"]=isset($video["url"])?$video["url"]:explode("url=",$video["signatureCipher"])[1];
            $videos[$i]["mimeType"]=explode(";",$video["mimeType"])[0];
            $videos[$i]["quality"]=isset($video["qualityLabel"])?$video["qualityLabel"]:$video["quality"];
            $i++;
        }
        $dataresult = [
            "title"=>$json["videoDetails"]["title"],
            "viewCount"=>$json["videoDetails"]["viewCount"],
            "channelId"=>$json["videoDetails"]["channelId"],
            "author"=>$json["videoDetails"]["author"],
            "thumbnail"=>$json["videoDetails"]["thumbnail"]["thumbnails"][2]["url"],
            "links"=>$videos];
        return $dataresult;
    }
}