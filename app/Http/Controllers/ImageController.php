<?php

namespace App\Http\Controllers;

use App\Models\Managers\ImageManager;

class ImageController extends Controller
{
    protected $imageManager;

    public function __construct(ImageManager $imageManager)
    {
        $this->imageManager = $imageManager;
    }

    /**
     * Receive the image from client and save it in a folder
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function postUpload()
    {
      try {
          //$file = Input::file('image');
          $file = Input::file('image');
          /*$input = array('image' => $file);
          $rules = array(
              'image' => 'image'
          );
          $validator = Validator::make($input, $rules);
          if ( $validator->fails() )
          {
              return Response::json(['success' => false, 'errors' => $validator->getMessageBag()->toArray()]);

          }*/
          //else {
          $destinationPath = 'uploads/';
          $filename = $file->getClientOriginalName();
          Input::file('image')->move($destinationPath, $filename);
          return Response::json(['success' => true, 'file' => asset($destinationPath.$filename)]);
          //}
      }
      catch (Exception $e)
      {
          Log::error('ImageController postUpload: '.$e);
          $this->logRepo->newLog('ImageController.php', 'ImageController.php', 'error catch', $e);
          return 0;
      }
    }

    /**
     * Return the upload image form
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Support\Facades\View
     */
    public function getUploadForm()
    {
      try {
          return View::make('image/upload-form');
      }
      catch (Exception $e)
      {
          Log::error('ImageController getUploadForm(): '.$e);
          $this->logRepo->newLog('ImageController.php', 'ImageController.php', 'error catch', $e);
          return 0;
      }
    }
}
