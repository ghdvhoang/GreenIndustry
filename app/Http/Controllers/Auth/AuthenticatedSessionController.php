<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function dataReplace($type = ""){
        //Need to add the schema on top of class, before using this function
        //use Illuminate\Support\Facades\Schema;
        //use DB;

        //Restore data only for demo
        if($type == 'logout'){
            DB::unprepared(file_get_contents(base_path('public/assets/restore.sql')));
        }

        //Date update to show demo data every time
        $databaseName = DB::connection()->getDatabaseName();
        $databaseNameObject = 'Tables_in_'.$databaseName;
        $tables = DB::select('SHOW TABLES');
        foreach($tables as $key => $table)
        {
            if($key%2 == 0){
                $current_timestamp = time() - rand(1, 86400);
            }else{
                $current_timestamp = time() - rand(1000, 40400);
            }

            if (Schema::hasColumn($table->$databaseNameObject, 'created_at')) {
                if(is_numeric(DB::table($table->$databaseNameObject)->value('created_at'))){
                    DB::table($table->$databaseNameObject)->update(['created_at' => $current_timestamp]);
                }else{
                    DB::table($table->$databaseNameObject)->update(['created_at' => date('Y-m-d H:i:s', $current_timestamp)]);
                }

            }

            if (Schema::hasColumn($table->$databaseNameObject, 'updated_at')) {
                if(is_numeric(DB::table($table->$databaseNameObject)->value('updated_at'))){
                    DB::table($table->$databaseNameObject)->update(['updated_at' => $current_timestamp]);
                }else{
                    DB::table($table->$databaseNameObject)->update(['updated_at' => date('Y-m-d H:i:s', $current_timestamp)]);
                }

            }

            if (Schema::hasColumn($table->$databaseNameObject, 'timestamp')) {
                DB::table($table->$databaseNameObject)->update(['timestamp' => $current_timestamp]);
            }

        }
    }
}
