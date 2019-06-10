<?php
uploader();
function uploader(){
	$file = isset($_FILES['file_my_1']) ? $_FILES['file_my_1'] : '';
	if(isset($_FILES['file_my_1'])) {

		$name = $file['name'];//得到图片名；
		$ext = $file['type'];//得到图片后缀；
	    $new_file_name = date('Y-m-d-H-i-s',time()) . $name;
	    $to_file_dir = 'uploadimg';
	 
	   $link_path ='./upload/'.$new_file_name;
	   move_uploaded_file($file["tmp_name"], $link_path);
			//copy($file["tmp_name"],$link_path);
	    echo json_encode([
	    	'code'=>0,
	    	'fielurl'=> $_FILES['file_my_1']
	    ]);
	    die;
	}
	echo 4;
	
}
function uploader1 ()
{
    // 获取表单上传文件 例如上传了001.jpg
    $file = $_FILE['file_my_1'];
    // 移动到框架应用根目录/public/uploads/ 目录下
    $info = $file->move(  './uploadimg' );
    
    if ( $info ) {
        halt($info->fileName);
        $data = [
            'name'       => $info['info']['name'] ,
            'filename'   => $info->getFilename() ,
            'path'       => 'uploads/' . $info->getSaveName() ,
            'extension'  => $info->getExtension() ,
            'createtime' => time() ,
            'size'       => $info->getSize() ,
        ];
        Db::name( 'attachment' )->insert( $data );
        echo json_encode( [ 'valid' => 1 , 'message' => HD_ROOT . 'uploads/' . $info->getSaveName() ] );
    }
    else {
        // 上传失败获取错误信息
        echo json_encode( [ 'valid' => 0 , 'message' => $file->getError() ] );
    }
}
