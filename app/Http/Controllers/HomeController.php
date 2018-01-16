<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Aliyun\Core\DefaultAcsClient;
use Aliyun\Core\Profile\DefaultProfile;
use Aliyun\Vod\CreateUploadVideoRequest;
use Aliyun\Oss\OssClient;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\VideoRequest;

class HomeController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
//        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(VideoRequest $request)
    {
        $file = $request->file('file');

        $localFile = $file->getRealPath();
//        Storage::put();
//        die(var_dump($file->getRealPath()));
//        Storage::put();

        $profile = DefaultProfile::getProfile("cn-shanghai", "LTAIAxmAQecGTfvI", "b2SfZz8U2GY2toiE6sHORsEv4whhog");
        $client = new DefaultAcsClient($profile);

        $request = new CreateUploadVideoRequest();
        $request->setTitle("title");        // 视频标题(必填参数)
        $request->setFileName("filename.mp4"); // 视频源文件名称，必须包含扩展名(必填参数)
        $request->setCoverURL("http://img.alicdn.com/tps/TB1qnJ1PVXXXXXCXXXXXXXXXXXX-700-700.png"); // 自定义视频封面(可选)
//      $request->setTags("标签1,标签2");
        $request->setAcceptFormat('JSON');
        $createRes = $client->getAcsResponse($request);

//  public 'UploadAddress' => string 'eyJFbmRwb2ludCI6Imh0dHBzOi8vb3NzLWNuLXNoYW5naGFpLmFsaXl1bmNzLmNvbSIsIkJ1Y2tldCI6Im91dC0yMDE3MTIwNjIxMTcyMTI1NS1tdzM5Nmo0ZHdvIiwiRmlsZU5hbWUiOiJzdi8yMGE3Mjk1My0xNjBmOWMyOTZiNi8yMGE3Mjk1My0xNjBmOWMyOTZiNi5tcDQifQ==' (length=212)
//  public 'VideoId' => string '0945e6dcc9d0433bbd1466888c558d1d' (length=32)
//  public 'RequestId' => string 'A87C625F-D7C1-46B4-BCEA-C5BDD6AA5E3F' (length=36)
//  public 'UploadAuth' => string 'eyJTZWN1cml0eVRva2VuIjoiQ0FJU3lnUjFxNkZ0NUIyeWZTaklyTENOTDlUa3FlZHJnWURkV0JQd3BuQVlXT0lkdkpEem1qejJJSHBOZTNocUIrMGZzUGt3bEdsVTZmZ2NsclVxRU1JY0dSS1lNNVV0c01nTm9GanhKcGZadjh1ODRZQURpNUNqUWR0eGhia2NtSjI4V2Y3d2FmK0FVQnZHQ1RtZDVMa1pvOWJUY1RHbFFDWnVXLy90b0pWN2I5TVJjeENsWkQ1ZGZybC9MUmRqcjhsbzF4R3pVUEcyS1V6U24zYjNCa2hsc1JZZTcyUms4dmFIeGRhQXpSRGNnVmJtcUpjU3ZKK2pDNEM4WXM5Z0c1MTlYdHlwdm9weGJiR1Q4Q05aNXo5QTlxcDlrTTQ5L2l6YzdQNlFIMzViNFJpTkw4L1o3dFFOWHdoaWZmb2JIYTlZcmZIZ21OaGx2dkRTajQzdDF5dFZPZVpjWDBha1E1dTdrdTdaSFArb0x0'... (length=1488)

        $videoId = $createRes->VideoId;
        $uploadAddress = json_decode(base64_decode($createRes->UploadAddress), true);
        $uploadAuth = json_decode(base64_decode($createRes->UploadAuth), true);


//        $localFile = 'C:\xampp\htdocs\laravel\storage\SampleVideo_1280x720_1mb.mp4';
        $ossClient = new OssClient($uploadAuth['AccessKeyId'], $uploadAuth['AccessKeySecret'], $uploadAddress['Endpoint'], false, $uploadAuth['SecurityToken']);
        $ossClient->setTimeout(86400 * 7);    // 设置请求超时时间，单位秒，默认是5184000秒, 建议不要设置太小，如果上传文件很大，消耗的时间会比较长
        $ossClient->setConnectTimeout(10);  // 设置连接超时时间，单位秒，默认是10秒
        return $data = $ossClient->uploadFile($uploadAddress['Bucket'], $uploadAddress['FileName'], $localFile);

        return view('home');
    }

}
