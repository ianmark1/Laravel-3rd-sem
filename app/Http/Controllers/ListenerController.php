<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use View;
use Redirect;

use App\Models\Album;
use App\Models\Listener;

class ListenerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $listeners = DB::table('listeners')
                        ->leftJoin('album_listener','listeners.id','=','album_listener.listener_id')
                        ->leftJoin('albums','albums.id','=','album_listener.album_id')
                        ->select('listeners.id','listeners.listener_name','albums.album_name')
                        ->get();
        return View::make('listener.index',compact('listeners'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // $albums = Album::pluck('album_name','id');
          //dd($albums);
        // return View::make('listener.create',compact('albums'));


        $albums = Album::with('artist')->get();
        // dd($album);
        // foreach($albums as $album ) {
        //     dump($album->artist->artist_name);
        // }
        return View::make('listener.create',compact('albums'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
    // $listener = new Listener;
    // $listener->listener_name = $request->listener_name;
    // $listener->save();
    // $listener = Listener::create($request->all());
   
    // if($request->album_id) {
    // foreach ($request->album_id as $album_id) {
    //     DB::table('album_listener')->insert(
    //         ['album_id' => $album_id, 
    //          'listener_id' => $listener->id
    //         ]
    //         );
    //     }
    // }

    $input = $request->all();
    // dd($request->album_id);
    $listener = Listener::create($input);
    if(empty($request->album_id)){
    foreach ($request->album_id as $album_id) {
        // DB::table('album_listener')->insert(
        //     ['album_id' => $album_id, 
        //      'listener_id' => $listener->id]
        //     );
        // dd($listener->albums());
        $listener->albums()->attach($album_id);
    }  //end foreach

}
return Redirect::to('listener')->with('success','New listener added!');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // $listener = Listener::find($id);
        
        // $album_listener = DB::table('album_listener')
        //                     ->where('listener_id',$id)
        //                     ->pluck('album_id')
        //                     ->toArray();
        // dd($album_listener);
        // $albums = Album::pluck('album_name','id');
        // dd($albums, $album_listener);



 // $listener = Listener::find($id);
        
        // $album_listener = DB::table('album_listener')
        //                     ->where('listener_id',$id)
        //                     ->pluck('album_id')
        //                     ->toArray();
        // // dd($album_listener);
        // $albums = Album::pluck('album_name','id');
        // // dd($albums, $album_listener);
        // return View::make('listener.edit',compact('albums','listener','album_listener'));

        $listener_albums = array();
        $listener = Listener::with('albums')->where('id', $id)->first();
        // $listener = Listener::with('albums')->get();

        // $albums = Album::with('artist')->where('id',$id)->take(1)->get();
        // dd($albums);

            // dump($listener);
            // dump($listener->listener_name);
            // dump($listener->albums);
            // foreach ($listener->albums as $album) {
            //      dump($album->album_name);
            //     }
        //$artist

        if(!(empty($listener->albums))){
            foreach($listener->albums as $listener_album){
                $listener_albums[$listener_album->id] = $listener_album->album_name;
            }
        }

        $albums = Album::pluck('album_name','id')->toArray();
        // dd($albums, $listener_albums);

// else {
        //     // $listener_albums[] = null;
        // }
        // $albums = Album::pluck('album_name','id')->toArray();
        // dd($albums,$listener_albums);
        // dd($albums,$listener->albums->toArray());
        // foreach ($listener->albums as $listener_album) {
        //     if(in_array($listener_album->album_name, $albums)){
        //         dump($albums);

        //     }
        //     else
        //         dump($albums);
            
        return View::make('listener.edit',compact('albums','listener','listener_albums'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
    //    $listener = Listener::find($id);

    //    $album_ids = $request->album_id;

    //    if(empty($album_ids)){
    //        DB::table('album_listener')
    //        ->where('listener_id',$id)
    //        ->delete();
    //    }

    //    else{
    //            DB::table('album_listener')
    //            ->where('listener_id',$id)
    //            ->delete();
          

    //        foreach($album_ids as $album_id){
    //            DB::table('album_listener')
    //            ->insert(['album_id' => $album_id,
    //            'listener_id' => $id]);
    //        }

    //    }
    //    $listener->update($request->all());
    $listener = Listener::find($id);
    $album_ids = $request->input('album_id');
     // dd($album_ids);

    //  if(empty($album_ids)){
    //      $listener->albums()->detach();
    //  }
    //  else {
    //      // foreach($album_ids as $album_id) {
    //      $listener->albums()->detach();
         
    //      $listener->albums()->attach($album_ids);
    //      // }
    //  }

    $listener->albums()->sync($album_ids);


$listener->update($request->all());


       return Redirect::route('listener.index')->with('success','lister updated!');





    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // $listener = Listener::find($id);

        // DB::table('album_listener')->where('listener_id',$id)->delete();

        // $listener->delete();

        $Listener = Listener::find($id);
        $Listener->albums()->detach();
        $Listener->delete();
        
        return Redirect::route('listener.index')->with('success','Listener deleted!');
    }
}
