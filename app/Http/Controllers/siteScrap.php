<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Goutte\Client;

class siteScrap extends Controller
{
    //
    public $token = "BQCCO5eyRkB5sib9nA1rzU2JUapz_mpek77LEpy8wQEx_ZQQrqi57TDxjYiX8c9QkefOzgFqcLp9OW8Rzgbjf4vqf_5lTqqtFG9vBAoFulmlmkVjRnDBiH_bweeqWqu-q21cQoYguxgpSTvzO0e-v4xuqdo08yThK4A5-6bqj3gr_gEd4rhUcKSC-eQY2iSnnqSlCeYZqsoX6WV0blM7My8qq6U1lQW9kySplp3U6wpbCMTqzYbyU2iKZ54w6i7nc6jN2ul6eaWD4npmob08NIAPkgMLzJNaKBFY3tr1NGLSlsIAarDK1R3iJXkiVzO89lOCyFM5R4Mu5A";
    public function site()
    {

        $res = $this->getSpotify();

        if(!empty($res))
        {

            //print_r($res); exit;
            print "Artist: ".$res['name']."<br/>";

            $url = $res['external_urls']['spotify'];
            $id = $res['id'];
            //print $url."========".$id; exit;
            print "Spotify URL: <a href='{$url}'>".$url."</a><br/>";

            $tracks = $this->getArtistTrack($id);
            //print_r($tracks); exit;
            $jioURL = $this->getJioSavanByTrack($tracks, $res['name']);
            //$jioURL = $this->getJioSavan($res['name']);
            if(!empty($jioURL))
            {
                print "JIO Savan URL: <a href='{$jioURL}'>".$jioURL."</a><br/>";
            }
            //$wynkURL = $this->getWynk($res['name']);
            $wynkURL = $this->getWynkByTrac($tracks, $res['name']);
            if(!empty($wynkURL))
            {
                print "Wynk URL: <a href='{$wynkURL}'>".$wynkURL."</a><br/>";
            }
            //$ganaURL = $this->getGana($res['name']);
            $ganaURL = $this->getGanaByTrac($tracks, $res['name']);
            if(!empty($ganaURL))
            {
                print "Gana URL: <a href='{$ganaURL}'>".$ganaURL."</a><br/>";
            }

        }
    }

    function getSpotify()
    {
        $headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer '.$this->token,
        ];


        //$endpoint = "https://api.spotify.com/v1/artists/198wHm8PoJ6EEKCkOI5kbi";
        //https://open.spotify.com/artist/1rd9JH6inVvjmshXW8hW1k
        //$endpoint = "https://api.spotify.com/v1/artists/1rd9JH6inVvjmshXW8hW1k";
        //https://open.spotify.com/artist/3kDnVxyMfvWBv32likJ0fh
        //$endpoint = "https://api.spotify.com/v1/artists/3kDnVxyMfvWBv32likJ0fh";
        //https://open.spotify.com/artist/5gVozagAcRKYCeAVnlC3Nk
        //$endpoint = "https://api.spotify.com/v1/artists/5gVozagAcRKYCeAVnlC3Nk";
        //https://open.spotify.com/artist/0VI5poXvvDVFaIPdL7M4rY
        //$endpoint = "https://api.spotify.com/v1/artists/0VI5poXvvDVFaIPdL7M4rY";
        //https://open.spotify.com/artist/65pmgBULHr82D3llNlHtB8
        $endpoint = "https://api.spotify.com/v1/artists/65pmgBULHr82D3llNlHtB8";
        $client = new \GuzzleHttp\Client([
            'headers' => $headers
        ]);

        $response = $client->request('GET', $endpoint, ['query' => []]);

        // url will be: http://my.domain.com/test.php?key1=5&key2=ABC;

        $statusCode = $response->getStatusCode();
        $content = $response->getBody();
        $resData = $content->getContents();
        $res = json_decode($resData, true);
        // print "<pre>";
        // print_r($res);
        return $res;
    }

    public function apiCall($endpoint='')
    {
        $headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ];


        //$endpoint = "https://api.spotify.com/v1/artists/198wHm8PoJ6EEKCkOI5kbi";
        $client = new \GuzzleHttp\Client([
            'headers' => $headers
        ]);

        $response = $client->request('GET', $endpoint);

        // url will be: http://my.domain.com/test.php?key1=5&key2=ABC;

        $statusCode = $response->getStatusCode();
        $content = $response->getBody();
        $resData = $content->getContents();
        $res = json_decode($resData, true);
        // print "<pre>";
        // print_r($res);
        return $res;
    }

    function getArtistTrack($id)
    {
        $headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer '.$this->token,
        ];

        $endpoint = "https://api.spotify.com/v1/artists/".$id."/albums";

        $client = new \GuzzleHttp\Client([
            'headers' => $headers
        ]);

        $response = $client->request('GET', $endpoint, ['query' => []]);
        $statusCode = $response->getStatusCode();
        $content = $response->getBody();
        $resData = $content->getContents();
        $res = json_decode($resData, true);
        //$tracks = $arr_column = array_column($res['items'], 'name');
        //print_r($res); exit;

        $tracks = array_map(function($item){
            return $item['name'];
        }, array_filter($res['items'],
            function($item) {
                return $item  ['album_type'] == 'single';
            }
        ));
        //print_r($tracks); exit;
        return $tracks;
    }

    public function getJioSavanByTrack($tracks='', $artist='')
    {
        print "========= JIO ========== <br/>";
        $urlLink = '';
        //print_r($tracks);
        foreach($tracks as $track){
            $endpoint = "https://saavn.me/search/all?query=".$track.' '.$artist;
            //print $endpoint; exit;
            $apiRes = $this->apiCall($endpoint);
            //print_r($apiRes); exit;
            $data = array();
           if($apiRes['status'] == 'SUCCESS')
            {
                if(!empty(!empty($apiRes['data']['artists']['results']))){
                    $data = $apiRes['data']['artists']['results'];
                    $artistData = array_filter($data, function($item) use($artist){
                        return $item['title'] == $artist;
                    });
                }
            }
            break;
        }
        if(!empty($artistData)){

            foreach($artistData as $artist)
            {
                if(!empty($artist['url']))
                    $urlLink = $artist['url'];
            }
        }else{
            $urlLink = $this->getJioSavan($artist);
        }

        return $urlLink;
    }

    public function getJioSavan($artistName='')
    {
        //print "========= JIO ========== <br/>";
        $endpoint = "https://saavn.me/search/artists?query=$artistName";
        $apiRes = $this->apiCall($endpoint);
        $urlLink = "";
        if($apiRes['status'] == 'SUCCESS')
        {
            $res = $apiRes['data']['results'];
            foreach($res as $artist){

                if($artistName == $artist['name'])
                {
                  $urlLink = $artist['url'];
                  break;
                }
            }
        }
        return $urlLink;
    }

    public function getWynk($artistName='')
    {
        //https://wynk.in/music/artist/balraj/wa_96f04899?q=balraj
        //https://wynk.in/music/artist/balraj/wa_96f04899

        //print "========= Wynk ========== <br/>";

        $endpoint = 'https://search.wynk.in/music/v4/search?display=true&count=50&offset=0&q='.$artistName.'&filter=artist';
        $apiRes = $this->apiCall($endpoint);
        $urlLink = "";
        if($apiRes['count'] > 0)
         {
            $res = $apiRes['items'];
            foreach($res as $artist){

                if(strtolower($artistName) == strtolower($artist['title']))
                {
                  $urlLink = "https://wynk.in/music/artist/".strtolower($artist['title'])."/".$artist['id'];
                  break;
                }
            }
        }
        return $urlLink;
    }

    public function getWynkByTrac($tracks='', $artistName='')
    {
        print "========= Wynk ========== <br/>";
        $urlLink = "";
        foreach($tracks as $track){

            $endpoint = "https://search.wynk.in/music/v4/search?display=true&count=50&offset=0&q=".$track." ".$artistName."&filter=artist";
            $apiRes = $this->apiCall($endpoint);
           if(!empty($apiRes['items']))
            {
                $data = $apiRes['items'];
                $artistData = array_filter($data, function($item) use($artistName){
                    return strtolower($item['title']) == strtolower($artistName);
                });
            }
            break;
        }
        if(!empty($artistData))
        {
            $urlLink = "https://wynk.in/music/";
            foreach($artistData as $artist)
            {
                $urlLink .= strtolower($artist['type'])."/";
                $urlLink .= strtolower($artist['title'])."/";
                $urlLink .= $artist['id']."/";
            }
        }else{
            $urlLink = $this->getWynk($artistName);
        }
        return $urlLink;
    }

    public function getGana($artistName='')
    {
        //https://gsearch.gaana.com/vichitih/go/v2?geoLocation=IN&query=balraj&content_filter=2&include=allItems&isRegSrch=0&webVersion=mix&rType=web&usrLang=Hindi,English,Punjabi&isChrome=1
        //https://gaana.com/artist/balraj

        //composer require weidner/goutte

        //print "========= Gana ========== <br/>";

        $client = new Client();
        $url = "https://gaana.com/artist/".$artistName;
        $crawler = $client->request('GET', $url);

        $jsonLdElements = $crawler->filter('script[type="application/ld+json"]')->each(function ($node) {
            return $node->text();
        });
        if(!empty($jsonLdElements[1])){
            $element = \json_decode($jsonLdElements[1], true);
            if(strtolower($element['name']) == strtolower($artistName))
            {
                return $element['url'];
            }
        }
        return '';
    }

    public function getGanaByTrac($tracks='', $artistName='')
    {
        //https://gsearch.gaana.com/vichitih/go/v2?geoLocation=IN&query=balraj&content_filter=2&include=allItems&isRegSrch=0&webVersion=mix&rType=web&usrLang=Hindi,English,Punjabi&isChrome=1
        //https://gaana.com/artist/balraj

        //https://gaana.com/search/songs/Nain%20Do%20balraj

        //composer require weidner/goutte

        $artistName = 'Sajjan Adeeb';
        print "========= Gana ========== <br/>";
        $urlLink = "";
        $client = new Client();

        //print_r($tracks); exit;
        foreach($tracks as $track){

            $track = 'mohabbat';
            $track_seo = str_replace(' ', '-', $track);

            $endpoint = "https://gaana.com/song/".strtolower($track_seo);
            $endpoint = "https://gaana.com/search/Sajjan%20Adeeb%20mohabbat";
            //https://gaana.com/search/Sajjan%20Adeeb%20mohabbat
            $crawler = $client->request('GET', $endpoint);
            $jsonLdElements = $crawler->filter('script[type="application/ld+json"]')->each(function ($node) {
                return $node->text();
            });
            print_r( $jsonLdElements ); exit;
            if(!empty($jsonLdElements[1])){
                $element = \json_decode($jsonLdElements[1], true);
                //print_r($element); exit;
                if(strtolower($element['name']) == strtolower($track))
                {
                    $artistData = array_filter($element['byArtist'], function($item) use($artistName){
                        return $item['name'] == $artistName;
                    });
                    print_r($artistData); exit;
                }
                if(!empty($artistData))
                {
                    foreach($artistData as $data)
                    {
                        $urlLink = $data['@id'];
                    }
                }
            }
            if(empty($urlLink))
            {
                $urlLink = $this->getGana($artistName);
            }
            return $urlLink;

        }
    }
}
