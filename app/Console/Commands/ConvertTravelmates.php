<?php

namespace App\Console\Commands;

class ConvertTravelmates extends ConvertBase
{

    protected $signature = 'convert:travelmates';


    public function convert()
    {
        $nodes = $this->getNodes('trip_forum_travelmate')
            ->join('content_field_reisitoimumine', 'content_field_reisitoimumine.nid', '=', 'node.nid')
            ->join('content_field_reisikestvus', 'content_field_reisikestvus.nid', '=', 'node.nid')
            ->join('content_field_millistkaaslastsoovidleida', 'content_field_millistkaaslastsoovidleida.nid', '=', 'node.nid')
            ->get();


        $this->info('Converting travelmates');
        $this->output->progressStart(count($nodes));

        foreach($nodes as $node) {   
  
            $node->field_reisitoimumine_value =  $this->formatTimestamp($node->field_reisitoimumine_value);

            $fields = [
                'field_reisitoimumine_value',
                'field_reisikestvus_value',
                'field_millistkaaslastsoovidleida_value',
            ];

            $node->body = $this->formatFields($node, $fields) . "\n\n" . $node->body;

            $this->convertNode($node, '\App\Content', 'travelmate');
            
            $this->convertNodeDestinations($node);
            $this->convertNodeTopics($node);

            $this->output->progressAdvance();

        }

        $this->output->progressFinish();
        
    }

    public function handle()
    {
        $this->convert();
    }

}