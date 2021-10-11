<?php
namespace Jdlx\Traits;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Symfony\Component\HttpFoundation\File\File;

trait WithAvatar{

    public function getAvatarUrlAttribute()
    {
       return $this->getAvatarUrl();
    }

    public function getAvatarUrl100Attribute()
    {
        return $this->getAvatarUrl("100");
    }

    public function getAvatarUrl400Attribute()
    {
        return $this->getAvatarUrl("400");
    }

    public function saveAvatar($avatar)
    {
        $filename = time() . '.' . $avatar->getClientOriginalExtension();

        $this->saveResized($avatar, $filename);
        $avatar->storeAs(
            "avatars", $filename, 'avatar'
        );

        $this->avatar = "avatars/".$filename;
        $this->save();
        return $this;
    }

    public static function saveResized($file, $filename)
    {
        $tmp = sys_get_temp_dir();

        $smallPath = $tmp . '/small_' . $filename;
        $mediumPath = $tmp . '/medium_' . $filename;

        Image::make($file)->resize(400, 400)->save($smallPath);
        Image::make($file)->resize(100, 100)->save($mediumPath);

        Storage::disk('avatar')->putFileAs('avatars', new File($smallPath), '100_100_'.$filename);
        Storage::disk('avatar')->putFileAs('avatars', new File($mediumPath),'400_400_'.$filename);
    }



    public function getAvatarUrl($size="original"){
        $file = $this->avatar;

        if(empty($file))
        {
            return $file;
        }

        if(stristr($file, "http"))
        {
            return $file;
        }

        $replace = "";
        switch($size)
        {
            case "100":
                $replace = "100_100_";
                break;
            case "400":
                $replace = "400_400_";
                break;
        }

        $file = str_replace('avatars/', "avatars/".$replace, $file);
        return Storage::disk("avatar")->url($file);
    }
}
