<?php

namespace Tombenevides\SaidQuotes;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class SaidQuote
{
    const BASE_ENDPOINT = "https://api.quotable.io/random";
    private Client $client;

    public function __construct(
        private ?string $author = null,
        ?Client $client = null
    )
    {
        $this->client = $client ?? new Client();
    }

    public function getQuote()
    {
        try{
            if(empty($this->author)){
                throw new \Exception("An author name is required");
            }

            $response = $this->client->get(self::BASE_ENDPOINT, [
                'query' => [
                    'author' => str_replace(' ', '-', $this->author)
                ]
            ])->getBody()->getContents();
    
            $quote = json_decode($response, true);
    
            return $quote['content'].' by '.ucwords($this->author);
        }catch(ClientException $e){
            return 'Error: Could not find any matching quotes for '.$this->author;
        }catch(\Exception $e){
            return 'Error: '.$e->getMessage();
        }	
    }

}