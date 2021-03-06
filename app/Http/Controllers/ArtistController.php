<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use View;
use Redirect;
use DB;
use App\Models\Artist;
use App\Models\Album;




class ArtistController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $artists = Artist::all();

        // $artists = DB::table('artists')->leftJoin('albums','artists.id','=','albums.artist_id')->get();

        // $artists = DB::table('artists')
        //     ->leftJoin('albums','artists.id','=','albums.artist_id')
        //     ->select('artists.id','albums.album_name','artists.artist_name',
        //     'artists.img_path')
        //     ->get();

        // return View::make('artist.index',compact('artists'));
        // $albums = Album::with('artist')->orderBy('album_name', 'DESC')->get();

        // return View::make('album.index', compact('albums'));


        $artists = Artist::with('albums')->get();

        return View::make('artist.index', compact('artists'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return View::make('artist.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // $input = $request->all();
        // Artist::create($input);
        // return Redirect::to('artist');

    
       
        $input = $request->all();
        $request->validate([
            'image' => 'required|mimes:jpeg,png,jpg,gif,svg',
        ]);
         if($file = $request->hasFile('image')) {
            
            $file = $request->file('image') ;
            $fileName = uniqid().'_'.$file->getClientOriginalName();
            // $fileName = $file->getClientOriginalName();
            // dd($fileName);
            $request->image->storeAs('images', $fileName, 'public');
        $input['img_path'] = 'images/'.$fileName;
            $artist = Artist::create($input);
            
        }
        return Redirect::to('artist');
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
       
           $artist = Artist::find($id);

           return View::make('artist.edit',compact('artist'));
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
        $artist = Artist::find($id);
    
        $artist->update($request->all());
        return Redirect::to('/artist')->with('success','Artist updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Album::where('artist_id',$id)->delete();
        // $artist = Artist::find($id);
        // File::delete('images/', $artist->img_path);
        // Artist::destroy($id);
        // return Redirect::to('/artist')->with('success','artist deleted!');

        $artist = Artist::find($id);

        $artist->albums()->delete();

        $artist->delete();
        $artist = Artist::with('albums')->get();
    }
}
