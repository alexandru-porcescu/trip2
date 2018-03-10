<?php

namespace App;

use Exception;
use Carbon\Carbon;
use DB;

class V2ContentVars
{
    protected $content;
    protected $unreadData;

    public function __construct(Content $content)
    {
        $this->content = $content;
    }

    public function __get($property)
    {
        if (method_exists($this, $property)) {
            return call_user_func([$this, $property]);
        }

        $message = '%s does not respond to the "%s" property or method.';

        throw new Exception(
            sprintf($message, static::class, $property)
        );
    }

    public function title()
    {
        if ($this->content->price) {
            return $this->content->title.' '.$this->content->price.'€';
        }

        return $this->content->title;
    }

    public function shortTitle()
    {
        return str_limit($this->content->title, 60);
    }

    public function body()
    {
        return format_body($this->content->body);
    }

    public function description()
    {
        return str_limit(format_description($this->content->body), 200);
    }

    public function created_at()
    {
        return format_date($this->content->created_at);
    }

    public function updated_at()
    {
        return format_date($this->content->updated_at);
    }

    public function commentCount()
    {
        return count($this->content->comments);
    }
    
    public function add_view()
    {
        $user = auth()->user();

        $table_name = with(new Activity)->getTable();
        $ip = DB::getPdo()->quote(request()->ip());
        $activity_id = (int) $this->content->id;
        $activity_type = DB::getPdo()->quote('App\Content');
        $type = DB::getPdo()->quote('view');
        $value = 1;

        if ($user) {
            $user_id = (int) $user->id;
        } else {
            $user_id = null;
        }

        $created_at = DB::getPdo()->quote(Carbon::now()->format('Y-m-d H:i:s'));
        $updated_at = DB::getPdo()->quote(Carbon::now()->format('Y-m-d H:i:s'));

        app('db')->select("INSERT INTO `$table_name` (`ip`, `activity_id`, `activity_type`, `type`, `value`, `user_id`, `created_at`, `updated_at`) 
        VALUES ($ip, $activity_id, $activity_type, $type, $value, ".($user_id ?? 'null').", $created_at, $updated_at) 
        ON DUPLICATE KEY UPDATE 
        `value`=`value` + 1,
        " . ($user_id ? "`user_id`=VALUES(`user_id`), " : "") . "
        `updated_at`=VALUES(`updated_at`)");
    }

    public function update_content_read()
    {
        $user = auth()->user();

        if ($user) {
            $unreadContent = UnreadContent::where('content_id', $this->content->id)->where('user_id', $user->id)->first();

            if (! $unreadContent) {
                $unreadContent = new UnreadContent;
                $unreadContent->content_id = $this->content->id;
                $unreadContent->user_id = $user->id;
            }

            $unreadContent->read_at = Carbon::now()->toDateTimeString();
            $unreadContent->save();
        }
    }

    public function isNew()
    {
        if (! $this->unreadData) {
            $this->unreadData = UnreadContent::getUnreadContent($this->content);
        }

        $comments_count = 0;
        if (isset($this->unreadData['count'])) {
            $comments_count = $this->unreadData['count'];
        }

        if ($this->content->comments->count() == 0 && $comments_count == 1) {
            return true;
        }

        return false;
    }

    public function firstUnreadCommentId()
    {
        if (! $this->unreadData) {
            $this->unreadData = UnreadContent::getUnreadContent($this->content);
        }

        if (isset($this->unreadData['first_comment_id'])) {
            return $this->unreadData['first_comment_id'];
        }
    }

    public function unreadCommentCount()
    {
        if (! $this->unreadData) {
            $this->unreadData = UnreadContent::getUnreadContent($this->content);
        }

        if (isset($this->unreadData['count']) && $this->content->comments->count() !== 0) {
            return $this->unreadData['count'];
        }

        return 0;
    }

    public function flagCount($flagType)
    {
        return $this->content->flags->where('flag_type', $flagType)->count();
    }
}
