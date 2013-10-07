<?php namespace Codeaqua\Image;

use Aws\Common\Aws;
use Aws\S3\Enum\CannedAcl;
use Aws\S3\Exception\S3Exception;
use Codeaqua\Models\Photo;
use Intervention\Image\Image as IntImage; // I am in the Codeaqua\Image namespace, and prefexing it not to confuse php.

define('AWS_CONFIG_FILE_PATH', app_path() . '/config/packages/aws-config.php');

class Resizer {

    /**
     * This is the path we are uploading
     * files before they are going to 
     * s3.
     *
     * @var string
     */
    const DEFAULT_UPLOAD_PATH = '/uploads/img/';

    /**
     * Name of the bucket at s3.
     *
     * @var string
     */
    const S3_BUCKET = 'enjoin';

    /**
     * Name of the Folder in the
     * s3 bucket.
     *
     * @var string
     */
    const S3_FOLDER_NAME = 'img/';

    /**
     * URL of the S3.
     *
     * @var string
     */
    const AWS_S3_URL = 'https://s3.amazonaws.com/';

    /**
     * describes the sizes of the photos need 
     * to be cropped.
     * @var array
     */
    protected static $sizes = [
        'thumb' => ['width' => 40, 'height' => 40],
        'thumb2x' => ['width' => 80, 'height' => 80],
        'thumb4x' => ['width' => 160, 'height' => 160],
        'square' => ['width' => 120, 'height' => 120],
        'square2x' => ['width' => 240, 'height' => 240],
        'square4x' => ['width' => 480, 'height' => 480],
        'large' => ['width' => 400, 'height' => 400],
        'large2x' => ['width' => 800, 'height' => 800],
        'large4x' => ['width' => 1600, 'height' => 1600],
    ];

    /**
     * The function that will be fired when we
     * are listening the queue jobs from sqs
     * @param  $job  SQS Queue job
     * @param  $data The data that is being hold by the queue
     */
    public function fire($job, $data)
    {
        $fileName = $data['imageUrl'];

        $photo = Photo::where('original', '=', $fileName)->first();

        print_r('Upload for: ' . $fileName . ' has started' . PHP_EOL);

        $photo->original = static::uploadOriginal($data);
        print_r('Uploading original version of ' . $fileName . ' has finished' . PHP_EOL);

        print_r(PHP_EOL . '---------------------------------------------------------------' . PHP_EOL);

        $photo->thumb    = static::cropAndUpload(static::$sizes['thumb'], $data, true);
        print_r('Uploading thumb version of ' . $fileName . ' has finished' . PHP_EOL);
        print_r(PHP_EOL . '---------------------------------------------------------------' . PHP_EOL);

        $photo->thumb2x  = static::cropAndUpload(static::$sizes['thumb2x'], $data, true);
        print_r('Uploading thumb2x version of ' . $fileName . ' has finished' . PHP_EOL);
        print_r(PHP_EOL . '---------------------------------------------------------------' . PHP_EOL);

        $photo->thumb4x  = static::cropAndUpload(static::$sizes['thumb4x'], $data, true);
        print_r('Uploading thumb4x version of ' . $fileName . ' has finished' . PHP_EOL);
        print_r(PHP_EOL . '---------------------------------------------------------------' . PHP_EOL);

        $photo->square   = static::cropAndUpload(static::$sizes['square'], $data, true);
        print_r('Uploading square version of ' . $fileName . ' has finished' . PHP_EOL);
        print_r(PHP_EOL . '---------------------------------------------------------------' . PHP_EOL);

        $photo->square2x = static::cropAndUpload(static::$sizes['square2x'], $data, true);
        print_r('Uploading square2x version of ' . $fileName . ' has finished' . PHP_EOL);
        print_r(PHP_EOL . '---------------------------------------------------------------' . PHP_EOL);

        $photo->square4x = static::cropAndUpload(static::$sizes['square4x'], $data, true);
        print_r('Uploading square4x version of ' . $fileName . ' has finished' . PHP_EOL);
        print_r(PHP_EOL . '---------------------------------------------------------------' . PHP_EOL);

        $photo->large    = static::cropAndUpload(static::$sizes['large'], $data);
        print_r('Uploading large version of ' . $fileName . ' has finished' . PHP_EOL);
        print_r(PHP_EOL . '---------------------------------------------------------------' . PHP_EOL);

        $photo->large2x  = static::cropAndUpload(static::$sizes['large2x'], $data);
        print_r('Uploading large2x version of ' . $fileName . ' has finished' . PHP_EOL);
        print_r(PHP_EOL . '---------------------------------------------------------------' . PHP_EOL);

        $photo->large4x  = static::cropAndUpload(static::$sizes['large4x'], $data);
        print_r('Uploading large4x version of ' . $fileName . ' has finished' . PHP_EOL);

        print_r(PHP_EOL . '---------------------------------------------------------------' . PHP_EOL);

        $photo->save();

        $path = public_path(). static::DEFAULT_UPLOAD_PATH . $data['imageUrl'];
        unlink($path);

        $job->delete();
    }

    /**
     * There is no cropping. Make the calls for
     * uploading locally uploaded file to s3.
     * @param  array $data Contains the path to the locally uploaded image.
     * @return String       The url of the uploaded image.
     */
    public static function uploadOriginal($data)
    {
        $image = new IntImage(public_path() . '/uploads/img/'. $data['imageUrl']);
        $resizedData['imageUrl'] = \Str::random(25) . (new \DateTime())->getTimeStamp() . '.jpg';
        $image->save(public_path() . static::DEFAULT_UPLOAD_PATH .  $resizedData['imageUrl'], 90); // save it as jpeg and make quality decreases.
        return static::uploadImageToS3($resizedData);
    }

    public static function cropAndUpload(array $size = array(), $data, $forceSquare = false)
    {
        // instanciate an Intervention\Image\Image instance.
        $image = new IntImage(public_path() . '/uploads/img/'. $data['imageUrl']);
        // if image's width < size's width
        // and
        // if image's height < size's height
        // then
        // leave the resolution as is
        // just make quality improvements
        // and save it as jpeg.
        
        if(!$forceSquare) {
            if($image->width < $size['width'] && $image->height < $size['height']) {
                print_r('Sizes are lower than we need.' . PHP_EOL);
                $resizedData['imageUrl'] = \Str::random(25) . (new \DateTime())->getTimeStamp() . '.jpg';
                $image->save(public_path() . static::DEFAULT_UPLOAD_PATH .  $resizedData['imageUrl'], 90); // save it as jpeg and make quality decreases.
                return static::uploadImageToS3($resizedData);
            }

            // if image's width > image's height
            // then
            // crop it's height.
            if($image->width > $image->height) {
                $size['height'] = $size['height'] * ($image->height / $image->width);
            }
            // if image's height > image's width
            // then
            // crop it's width
            else if($image->height > $image->width) {
                $size['width'] = $size['width'] * ($image->width / $image->height);
            }
        }

        // there is no else statement because
        // if the image's width = image's height
        // then
        // keep the size equal
        // because it means the image is square
        // and we can crop it to the resolutions we want.

        $resizedData['imageUrl'] = \Str::random(25) . (new \DateTime())->getTimeStamp() . '.jpg';

        $image->grab($size['width'], $size['height']) // grab the necessary photo of the sizes we want
            ->save(public_path() . static::DEFAULT_UPLOAD_PATH . $resizedData['imageUrl'], 90); // save it as jpeg and make quality decreases.


        print_r(PHP_EOL . 'Resized Image path: ' . $resizedData['imageUrl'] . ' - sizes: ' . $size['width'] . 'x' . $size['height'] . PHP_EOL);

        return static::uploadImageToS3($resizedData);
    }

    /**
     * Uploads the given image to s3.
     * @param  array $data Contains the path to the locally uploaded image.
     * @return String       The url of the uploaded image.
     */
    public static function uploadImageToS3($data)
    {
        // generate the s3 instance.
        $s3 = Aws::factory(AWS_CONFIG_FILE_PATH)->get('s3');

        // the localimage url is saved as
        // 'imageUrl' in the data array.
        $imageUrl = $data['imageUrl'];

        // We are exploding the file name and
        // extension and putting it into local
        // variables.
        list($filename, $extension) = explode('.', $imageUrl);

        // we are finding the locally uploaded
        // image so that we can upload it to s3.
        $path = public_path(). static::DEFAULT_UPLOAD_PATH . $imageUrl;

        // generate a unique name depending on the 
        // timestamp to upload s3. We are keeping all
        // of the images in one bucket. So that we need
        // to make the names unique.
        $fileNameForS3 = \Str::random(20) . (new \DateTime())->getTimeStamp() . '.' . $extension;

        // Upload a publicly accessible file. File size, file type, and md5 hash are automatically calculated by the SDK
        try {
            $s3->putObject(array(
                'Bucket' => static::S3_BUCKET,
                'Key'    => static::S3_FOLDER_NAME . $fileNameForS3,
                'Body'   => fopen($path, 'r'),
                'ACL'    => CannedAcl::PUBLIC_READ // so that it can be accesible from anywhere.
            ));
        } catch (S3Exception $e) {
            $error = ['error' => true, 'message' => 'The file cannot be uploaded.', 'image_type' => $name];
            return Response::json($error, 404);
        }

        unlink($path);

        return static::AWS_S3_URL . static::S3_BUCKET . '/' . static::S3_FOLDER_NAME . $fileNameForS3;
    }
}