<?php

namespace App\Http\Controllers\Widgets;

use App\Server;
use App\Widget;
use App\Http\Controllers\Controller;

class MainController extends Controller
{
    public function add()
    {
        if(!auth()->user()->isAdmin() && Widget::where("user_id",auth()->user()->id)->count() > env("USER_WIDGET_COUNT")){
            return respond("Widget kotanızı aştınız, yeni widget ekleyemezsiniz");
        }
        if(Widget::where([
            "name" => explode(':',request('widget_name'))[0],
            "text" => explode(':',request('widget_name'))[3],
            "title" => explode(':',request('widget_name'))[1],
            "user_id" => auth()->user()->id,
            "extension_id" => extension()->id,
            "server_id" => server()->id,
            "function" => explode(':',request('widget_name'))[0],
            "type" => explode(':',request('widget_name'))[2]
        ])->exists()){
            return respond("Bu sunucu için aynı widget daha önce zaten eklenmiş",201);
        }
        $widget = new Widget([
            "name" => explode(':',request('widget_name'))[0],
            "text" => explode(':',request('widget_name'))[3],
            "title" => explode(':',request('widget_name'))[1],
            "user_id" => auth()->user()->id,
            "extension_id" => extension()->id,
            "server_id" => server()->id,
            "function" => explode(':',request('widget_name'))[0],
            "type" => explode(':',request('widget_name'))[2]
        ]);
        $widget->save();
        return respond('Widget Eklendi',200);
    }

    public function settings()
    {
        $widgets = Widget::where('user_id',auth()->id())->get();
        foreach ($widgets as $widget){
            $widget->server_name = Server::where('id',$widget->server_id)->first()->name;
        }
        return view('widgets.settings',[
            "widgets" => $widgets
        ]);
    }

    public function update_orders()
    {
      foreach (json_decode(request('widgets')) as $widget) {
        $data = Widget::find($widget->id);
        $data->order = $widget->order;
        $data->save();
      }
      return respond('Widgetlar güncellendi',200);
    }
}
