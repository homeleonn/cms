<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Auth;
use Session;

class UserController extends Controller
{
	public function index()
	{
//		var_dump(Auth::check());
//		dd(session()->all());
//		dd(session()->all());
//		session()->remove('user');
//		dd($user = User::where('email', 'test@ukr.net')->first(['id', 'name', 'accesslevel']), password_verify('123456', $user['password']), get_class_methods($user), $user->getAttributes());
		return view('user.' . (Auth::check() ? 'index' : 'login'));
	}

	public function auth(Request $request)
	{
		$fields = $request->only(['email', 'password']);
		
		$this->validate($request, [
			'email' => 'required|email',
			'password' => 'required',
		]);
		
		if (Auth::attempt($fields)) {
			$user = User::where('email', $fields['email'])->first(['id', 'name', 'accesslevel']);
			Session::push('id', $user['id']);
			Session::put('user', $user->getAttributes());
			
			return redirect('/user');
		}
	}
	
	public function logout()
	{
		Auth::logout();
		
		return redirect('/user');
	}
}
