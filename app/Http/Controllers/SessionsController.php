<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Auth;

class SessionsController extends Controller
{

    //只让未登录用户访问登录页面
    public function __construct()
    {
        $this->middleware('guest', [
            'only' => ['create']
        ]);
    }


    public function create()
    {
    	return view('sessions.create');
    }


    public function store(Request $request)
    {
    	$credentials = $this->validate($request, [
    		'email' => 'required|email|max:255',
    		'password' => 'required',
    	]);

    	/*attempt 方法可以让我们很方便的完成用户的身份认证操作
    	如果用户被找到：
 		1:先将传参的 password 值进行哈希加密，然后与数据库中password 字段中已加密的密码进行匹配；
		2:如果匹配后两个值完全一致，会创建一个『会话』给通过认证的用户。会话在创建的同时,也会种下一个名为 laravel_session 的 HTTP Cookie，以此 Cookie 来记录用户登录状态，最终返回 true；
		3:如果匹配后两个值不一致，则返回 false；
		4:如果用户未找到，则返回 false。*/
    	if(Auth::attempt($credentials, $request->has('remember'))) {
            if(Auth::user()->activated){
                session()->flash('success', '欢迎回来！');
                return redirect()->intended(route('users.show', [Auth::user()]));
            } else {
                Auth:logout();
                session()->flash('waring', '你的账号未激活，请检查邮箱中的注册邮件进行激活。');
                return redirect('/');
            }
    		
    	} else {
    		session()->flash('danger', '很抱歉，您的邮箱和密码不匹配');
    		return redirect()->back();
    	}
    }


    public function destroy()
    {
    	Auth::logout();
    	session()->flash('success', '您已经退出！');
    	return redirect('login');
    }
}
