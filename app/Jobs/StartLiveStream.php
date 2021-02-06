<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\Process\Process;

class StartLiveStream implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $stream_url_arr;
    protected $file_uri;
    protected $post;

    /**
     * Create a new job instance.
     *
     * @param array $stream_url_arr
     * @param string $file_uri
     */
    public function __construct($stream_url_arr, $file_uri)
    {
        $this->stream_url_arr = $stream_url_arr;
        $this->file_uri = $file_uri;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $loop = false; // loop file ->>> see http://trac.ffmpeg.org/wiki/Concatenate
                        // https://video.stackexchange.com/questions/12905/repeat-loop-input-video-with-ffmpeg
        //$file_uri = 'http://commondatastorage.googleapis.com/gtv-videos-bucket/sample/TearsOfSteel.mp4';
        $file_uri = $this->file_uri;

        /**** Sample code for looping ****/
        /*
        $loop_times = 3;
        $loop_file = "";
        for ($i=0; $i < $loop_times; $i++) {
            $loop .= "file '".$file_uri."'\n";
        }
        */

        $enable_watermark = false;
        $watermark_cmd = '';

        $stream_url = array_pop($this->stream_url_arr);
        $output_encoding = 'flv';
        $livestream_exec_code = 'ffmpeg -rtbufsize 128M -re  -i '
            .($loop?' concat -safe 0 ':'')
            .' "'.$file_uri.'"'
            .' '
            .($enable_watermark?$watermark_cmd:'')
            .' -acodec libmp3lame -ar 44100 -b:a 128k -pix_fmt yuv420p -profile:v baseline -bufsize 3500k'
            .' -vb 3000k -maxrate 3500k -deinterlace -vcodec libx264 -preset veryfast -g 30 -r 30 -f '.$output_encoding.' '
            .' "'.$stream_url.'" ';

        while(count($this->stream_url_arr)){
            $livestream_exec_code .= ' -c copy -f '.$output_encoding.' "'.array_pop($this->stream_url_arr).'" ';
        }

        $process = Process::fromShellCommandline($livestream_exec_code);
        $process->disableOutput();
        $process->start();

    }

}
