<?php
/**
 * Gallery
 *
 * @package     mobicms
 * @subpackage  gallery
 * @author      Jose Sibande <jbsibande@gmail.com>
 */

class Site_Gallery extends FController
{
  
  public function index()
  {
    if ( ! $this->process_gallery_request())
    {
      return $this->render('500.html');
    }

    $this->render('gallery/index.html', $this->data);
  }
  
  public function user_gallery($request)
  {
    if ( ! $this->process_gallery_request($request))
    {
      return $this->render('500.html');
    }
    if ( ! (bool) $this->user )
    {
      return $this->render('404.html'); 
    }

    $files = $this->db->query('SELECT * FROM files f WHERE f.uid=\''.$this->data['user']['object']['id'].'\'');
    $files = $files->fetchAll();

    $this->data['files'] = $files;
    $this->data['request'] = $request;
    $this->render('gallery/user.html', $this->data);
  }

  public function user_file($request)
  {
    if ( ! $this->process_gallery_request($request))
    {
      return $this->render('500.html');
    }

    $file = $this->db->prepare('SELECT u.username, f.id, f.original, f.datetime  FROM files f '.
			       'INNER JOIN users u ON (f.uid = u.id)  WHERE f.id=:id LIMIT 1');
    $file->execute(array(':id'=>$request['route']['file_id']));
    $file = $file->fetch();

    if (( ! (bool) $this->user) or ( ! $file))
    {
      return $this->render('404.html'); 
    }

    $this->data['file'] = $file;

    $this->render('gallery/view_file.html', $this->data);
  }

  public function set_profile_pic($request)
  {
    if ( ! $this->process_gallery_request($request))
    {
      return $this->render('500.html'. $this->data);
    }

    $file = $this->db->prepare('SELECT f.id, f.uid  FROM files f WHERE f.id=:id LIMIT 1');
    $file->execute(array(':id'=>$request['route']['file_id']));
    $file = $file->fetch();

    if (( ! (bool) $this->user) or ( ! $file))
    {
      return $this->render('404.html', $this->data); 
    }
    
    if (preg_match('/^(?P<image_uri>.+)\/default\/?/', $request['route'][0], $matches))
    {
      
      $image_uri = $matches['image_uri'];
      $profile = $this->db->query('SELECT id FROM profile WHERE uid=\''.$file['uid'].'\'');
      // activate user profile
      if ( ! $profile->fetch())
      {
	$profile = $this->db->query('INSERT INTO profile (uid) VALUES (\''.$file['uid'].'\')');
      }
      $profile = $this->db->query('UPDATE profile SET profile_pic = \''.$file['id'].
				  '\' WHERE uid = \''.$file['uid'].'\'');

      header('Location: http://'.$_SERVER['HTTP_HOST'].'/'.$image_uri);
    }
    else
    {
      return $this->render('404.html', $this->data); 
    }
  }

  public function upload()
  {
    if ( ! $this->process_gallery_request())
    {
      return $this->render('500.html');
    }

    $rules = require(dirname(__FILE__).'/forms/gallery.php');
    $form = new FForm_Form($rules);
    
    if ( ! (empty($_POST) and empty($_FILES)))
    {
      if ($form->validate())
      {
	$user_folder = PROJECT_ROOT_DIR.'/'.APPLICATION_DIR.'/static/gallery/'.
	  strtolower($this->data['user']['object']['username']);
	
	$image_widths = array(60, 120, 176, 240, 480);
		
	$file_type = $form->data['file']['value']['type'];
	$original = $form->data['file']['value']['name'];
	$file_name = explode('.', $original);
	$ext = strtolower($file_name[count($file_name)-1]);
	$file_name = implode('.', array_slice($file_name, 0, count($file_name)-1));	
	$file_name = FHelper::slugify($file_name);
	// save file details in database
	$file_data = $this->db->prepare('INSERT INTO files (uid, original, file, file_type, category) '.
					'VALUES (:uid, :original, :file, :file_type, :category)');
	$file_data->execute(array(':uid'=> $this->data['user']['object']['id'],
				  ':original'=>$original,
				  ':file'=>$file_name,
				  ':file_type'=>$file_type,
				  ':category'=>$this->category['id']));
	$file_id = $this->db->prepare('SELECT id FROM files WHERE file=:file LIMIT 1');
	$file_id->execute(array(':file'=>$file_name));
	$file_id = $file_id->fetch();
	$file_id = $file_id['id'];

	// set as [user_forlder]/[db file_name]-[db row id].[ext in lower case]
	$file_path = $user_folder.'/'.$file_name.'-'.$file_id.'.'.$ext;
	
	FHelper::mkdirs($user_folder, $image_widths);

	// save image
	move_uploaded_file($form->data['file']['value']['tmp_name'],
			   $file_path);
	
	// resize image
	$this->_save_resized_images($file_path, $image_widths);

	header('Location: http://'.$_SERVER['HTTP_HOST'].'/gallery');
      }
    }
    
    $this->data['form'] = $form;

    $this->render('gallery/upload.html', $this->data);
  }
  
  /**
   * Resizes and saves images in dirs with width labels.
   *
   * @param   string  full file path
   * @param   array   supported widths
   * @return  void
   */
  public function  _save_resized_images($file_path, $widths=array())
  {    
    $image = KIL_Image::factory($file_path);
    $name = explode('/', $file_path);
    $name = $name[count($name)-1];
    
    foreach ($widths as $width)
    {
      $im = clone $image;
      $im->resize($width, NULL);
      $im->save(dirname($file_path).'/'.$width.'/'.$name);
    }
  }
  
  public function process_gallery_request($request=NULL)
  {
    $gallery = $this->db->query('SELECT id FROM content_type WHERE lower(table_id) = lower(\'gallery\')');
    $this->gallery = $gallery->fetch();
    if ( ! (bool) $this->gallery)
    {
      return FALSE;
    }
    $category = $this->db->query('SELECT id, label FROM category WHERE lower(label)=lower(\'Default\') LIMIT 1');
    $this->category = $category->fetch();
    if ( ! $this->category)
    {
      return FALSE;
    }

    if ( (bool) $request)
    {
      $user = $this->db->prepare('SELECT id, username, email, udatetime, datetime '.
				 'FROM users WHERE lower(username) = lower(:username) LIMIT 1');
      $user->execute(array(':username' => $request['route']['username']));
      $this->user = $user->fetch();
    }
    return TRUE;
  }

}
