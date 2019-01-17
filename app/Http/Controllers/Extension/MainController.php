<?php

namespace App\Http\Controllers\Extension;

use App\Extension;
use App\Http\Controllers\Controller;
use App\Server;

class MainController extends Controller
{
    public function all()
    {
        if (!Extension::where('_id', request('extension_id'))->exists()) {
            return redirect(route('home'));
        }

        // Get all Servers which have this extension.
        $servers = Server::all()->filter(function($value,$key){
            return array_key_exists(request('extension_id'),$value->extensions);
        });
        $servers = Server::filterPermissions($servers);
        // Go through servers and create a city list, it will be used in javascript to highlight cities in map.
        $cities = [];
        foreach ($servers as $server) {
            if(!in_array($server->city,$cities)){
                array_push($cities,$server->city);
            }
        }
        // If user have only servers in one city, redirect to it.
        if(count($cities) == 1){
            return redirect(route('extension_city',[
                "extension_id" => request('extension_id'),
                "city" => $cities[0]
            ]));
        }
        if($cities == null){
            return respond('Bu özelliği kullanabileceğiniz hiçbir sunucunuz yok.');
        }
        return view('feature.index', [
            "cities" => implode(',',$cities)
        ]);
    }

    public function city()
    {
        // Get all Servers which have this extension.
        $servers = Server::all()->filter(function($value,$key){
            return array_key_exists(request('extension_id'),$value->extensions);
        });
        $servers = Server::filterPermissions($servers);

        // Get Extension Name
        $extension = Extension::where('_id',request('extension_id'))->first();

        return view('feature.city', [
            "servers" => $servers,
            "name" => $extension->name
        ]);
    }
}
