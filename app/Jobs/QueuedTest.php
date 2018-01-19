<?php
 
namespace App\Jobs;
 
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
 
 
class QueuedTest implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;
    private $data;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        //
        $data['date']=date("Y-m-d H:i:s");
        $this->data = $data;
    }
 
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        echo rand(1, 10);
        echo json_encode($this->data);
    }
}