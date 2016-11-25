<?php

namespace LKDevelopment\UptimeMonitorAPI\Http\Controller;

use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Spatie\UptimeMonitor\Models\Monitor;
use Spatie\Url\Url;

class MonitorController extends Controller
{
    use ValidatesRequests;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Monitor::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, config('laravel-uptime-monitor-api.validationRules'));
        $url = Url::fromString($request->get('url'));
        Monitor::create([
            'url' => trim($url, '/'),
            'look_for_string' => $request->get('look_for_string') ?? '',
            'uptime_check_method' => $request->has('look_for_string') ? 'get' : 'head',
            'certificate_check_enabled' => $url->getScheme() === 'https',
            'uptime_check_interval_in_minutes' => $request->get('uptime_check_interval_in_minutes'),
        ]);

        return response()->json(['created' => true]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Monitor::findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, config('laravel-uptime-monitor-api.validationRules'));

        $monitor = Monitor::findOrFail($id);
        $url = Url::fromString($request->get('url'));
        $look_for_string = ($request->has('look_for_string')) ? $request->get('look_for_string') : $monitor->look_for_string;
        $monitor->update([
            'url' => $request->get('url'),
            'look_for_string' => $look_for_string,
            'uptime_check_method' => $request->has('look_for_string') ? 'get' : 'head',
            'certificate_check_enabled' => $url->getScheme() === 'https',
            'uptime_check_interval_in_minutes' => $request->get('uptime_check_interval_in_minutes'),
        ]);

        return response()->json(['updated' => true]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $monitor = Monitor::findOrFail($id);
        $monitor->delete();

        return response()->json(['deleted' => true]);
    }
}
