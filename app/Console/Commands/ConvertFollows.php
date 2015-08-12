<?php

namespace App\Console\Commands;

use DB;
use App\Follow;

class ConvertFollows extends ConvertBase
{

    protected $signature = 'convert:follows';

    public function getFollows()
    {
    
        return DB::connection($this->connection)
            ->table('sein_subscribe')
            ->latest('subscribe_id')
            ->skip($this->skip)
            ->take($this->take);
    
    }

    public function convertFollows()
    {
        $follows = $this->getFollows()->get(); 

        $this->info('Converting follows (subscriptions)');
        $this->output->progressStart(count($follows));

        foreach($follows as $follow)
        {   
            if ($this->getNode($follow->nid) && $this->isUserConvertable($follow->uid)) {

                if ($this->convertNode($this->getNode($follow->nid), '\App\Content', 'forum')) {

                    $model = new Follow;

                    $model->id = $follow->subscribe_id;
                    $model->user_id = $follow->uid;
                    $model->status = $follow->active;
                    $model->followable_type = 'App\Content';
                    $model->followable_id = $follow->nid;

                    $model->save();

                    $this->convertUser($follow->uid);
              
                }

                $this->output->progressAdvance();

            }
              
        }

        $this->output->progressFinish();

    }

    public function handle()
    {
        
        $this->convertFollows();
        
    }

}
