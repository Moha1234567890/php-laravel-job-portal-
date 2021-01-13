<?php

namespace App\Http\Controllers\Jobs;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Email;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\JobsRequest;
use App\Mail\ApplyMail;

use App\Models\SavedJob;

use App\Models\Job;


class JobsController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index() {


        return view('jobs.index')->with('jobs', Job::all());



    }

    public function create() {

       return  view('jobs.create');
    }


    public function store(Request $request) {

        Request()->validate([

            'image'        => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'email'        => 'required|string|max:100',
            'jobtitle'     => 'required|string|max:100',
            'location'     => 'required|string|max:100',
            'region'       => 'required|string|max:100',
            'jobtype'      => 'required',
            'vacancy'      => 'required',
            'ex'           => 'required',
            'sal'          => 'required',
            'jobdesc'      => 'required|max:900',
            'respon'       => 'required|max:900',
            'ben'          => 'required|max:900',
            'user_id'      => 'required',
            'edu'          => 'required|max:900',
            'jobcategory'  => 'required',
            'gender'       => 'required',
            'companyname'  => 'required|string',
            'website'      => 'required|string',
            'linkedin'     => 'required|string'



        ]);


        $job = Job::create([
            'user_id'     => $request->user_id,
            'email'       => $request->email,
            'jobtitle'    => $request->jobtitle,
            'location'    => $request->location,
            'region'      => $request->region,
            'jobtype'     => $request->jobtype,
            'vacancy'     => $request->vacancy,
            'gender'      => $request->gender,
            'edu'         => $request->edu,
            'ex'          => $request->ex,
            'sal'         => $request->sal,
            'sal'         => $request->sal,
            'jobdesc'     => $request->jobdesc,
            'respon'      => $request->respon,
            'ben'         => $request->ben,
            'jobcategory' => $request->jobcategory,
            'companyname' => $request->companyname,
            'website'     => $request->website,
            'linkedin'    => $request->linkedin,
            'image'       =>  $request->image->store('company_logos', 'public')
        ]);



        if($job)
            return redirect()->back()->with(['success' => 'admins will get back to verifiy you job']);

        else
            return abort('404');


    }


    public function show($id) {


        $job = Job::findOrFail($id);

        $job_counter = Email::where( 'job_id_email', $id)->count();

       $jobx =  SavedJob::with('jobs')->where('job_id', $id)->whereHas('jobs', function ($q) {

           $q->select('user_id', 'job_id','id');

       })->where('user_id', Auth::user()->id)->get()->first();



       if($id)
           return view('jobs.show', compact('jobx', 'job','job_counter'));

       else
           return abort('404');



    }

    public function loadCounter($id) {
        $job_counter = Email::where( 'job_id_email', $id)->count();



        echo $job_counter;

    }

    public function send(Request $request) {

        Request()->validate([

            'image' => 'required|file|mimes:pdf,doc,txt|max:2048',


        ]);


        $data = [
            'id'      => $request->id,
            'to'      => $request->to,
            'from'    => $request->from,
            'subject' => $request->subject,
            'image'   => $request->file('image')

        ];

//        Request()->validate([
//
//            'cv' => 'required|file|mimes:pdf,doc,txt|max:2048',
//
//
//        ]);






       Email::create([
            'job_id_email' => $data['id'],
            'to_user'      => $data['to'],
            'from_user'    => $data['from']


        ]);






        $to = $data['to'];
        $id = $data['id'];


        $mail = \Mail::to($to)->send(new ApplyMail($data));


        if($mail)

            return redirect()->route('browse.one.job', ['id' => $id]);



    }

    public function save(Request $request) {



        $savIt = SavedJob::create([

            'job_id'       => $request->job_id,
            'user_id'      => $request->user_id,
            'pic'          => $request->pic,
            'job_title'    => $request->job_title,
            'company_name' => $request->company_name,
            'location'     => $request->location,
            'region'       => $request->region,
            'job_type'     => $request->job_type

        ]);








    }

    public function delete($id) {

       $job_del = SavedJob::where('job_id', $id)->where('user_id',Auth::user()->id);

        $delete = $job_del->delete();





    }

    public function cats() {


        $cats = Category::select()->get();
        return view('jobs.cats', compact('cats', $cats));

    }


    public function category($name) {

        $category = Job::with('getCategory')->where('jobcategory', $name)->where('status', 1)->paginate(3);

        if($category) {
            return view('jobs.category', compact('category'));
        } else {
            return redirect('home');
        }
    }


    public function cities() {


       $cities = Job::select('region')->distinct()->get();

        return view('jobs.cities', compact('cities'));

    }

    public function city($city) {

        $region = Job::select()->where('region', $city)->where('status', 1)->paginate(3);

       if($region) {
           return view('jobs.city', compact('region'));
       } else {
           return redirect('home');
       }
    }

    public function search(Request $request) {

        $keyword = $request->get('keyword');

        $state = $request->get('state');

        $selectCate = $request->get('selectCate');

        $getJobs = Job::select()->where(function($q) use($keyword, $state, $selectCate) {
            $q->where('jobtitle', 'like', "{$keyword}")
                ->where('region', 'like', "{$state}")
                ->where('jobcategory', 'like', "{$selectCate}");

        })->paginate(3);

        $getJobs_counter = $getJobs->count();






        return view('jobs.search', compact('getJobs', 'getJobs_counter'));
    }

    public function jobTitle($job_title) {



       $getJobsTitle = Job::select()->where('jobtitle', $job_title)->where('status', 1)->paginate(3);



        return view('jobs.jobTitle', compact('getJobsTitle'));
    }







}
