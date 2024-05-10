<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Seller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends Controller


{

    public function logout(Request $request)
{
    // تحديد الـ guard  إذا كان الطلب قادم من بائع
    $isSeller = $request->is('api/seller/*');
    $guard = $isSeller ? 'seller' : 'api';

    // التحقق من صحة الـ token وجود المستخدم الحالي
    if (Auth::guard($guard)->check()) {
        Auth::guard($guard)->logout();

        // الرد برسالة نجاح تسجيل الخروج
        return response()->json(['message' => 'Logout Successful']);
    }

    // الرد برسالة فشل في حالة عدم وجود مستخدم مصادق
    return response()->json(['message' => 'Can not log out, user not found'], 401);
}

    

public function userProfile(Request $request)
{
    // تحديد ما إذا كان الطلب قادم من بائع
    $isSeller = $request->is('api/seller/*');
    $guard = $isSeller ? 'seller' : 'api';

    // استرجاع بيانات المستخدم الحالي من الـ guard المناسب
    $user = Auth::guard($guard)->user();

    // الاستجابة بمعلومات المستخدم
    return response()->json($user);
}


public function login(Request $request)
{
    $credentials = $request->only(['email', 'password']);

    $isSeller = $request->is('api/seller/*');
    $guard = $isSeller ? 'seller' : 'api';

    if (! $token = Auth::guard($guard)->attempt($credentials)) {
        return response()->json(['error' => 'Unauthorized'], 401);
    }
    
    return $this->createNewToken($token, 'Login Successful');
}



public function refresh(Request $request)
{
    // تحديد ما إذا كان الطلب قادم من بائع
    $isSeller = $request->is('api/seller/*');
    $guard = $isSeller ? 'seller' : 'api';

    try {
        // إعادة إصدار توكن جديد
        if ($token = Auth::guard($guard)->refresh()) {
            // الاستجابة مع الـ token الجديد وبيانات المستخدم
            return $this->createNewToken($token, $guard);
        }
    } catch (TymonJWTAuthExceptionsTokenInvalidException $e) {
        // إذا كان التوكن غير صالح
        return response()->json(['error' => 'Token is invalid'], 401);
    } catch (TymonJWTAuthExceptionsTokenExpiredException $e) {
        // إذا كان التوكن قد انتهت صلاحيته
        return response()->json(['error' => 'Token is expired'], 401);
    } catch (TymonJWTAuthExceptionsJWTException $e) {
        // لأي استثناءات أخرى
        return response()->json(['error' => 'Token is missing'], 401);
    }
}



public function register(Request $request)
    {
        // تحديد ما إذا كان الطلب قادم من الـ api للـ sellers
        $isSeller = $request->is('api/seller/*');
    
        // اختيار جدول البيانات المناسبة استنادا على ما إذا كان الطلب لبائع أم لا
        $model = $isSeller ? new Seller : new User;
    
        // مصادقة بيانات الطلب
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:' . $model->getTable(),
            'password' => 'required|string|min:6',
        ]);
    
        // إذا فشلت عملية التحقق من البيانات، أرجع رسالة خطأ
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
    
        // إنشاء البائع أو المستخدم العادي بناءً على نوع الطلب
        if ($isSeller) {
            $user = Seller::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
            ]);
            $guard = 'seller';
        } else {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
            ]);
            $guard = 'api';
        }
    
        // تسجيل الدخول وإنشاء الـ token للمستخدم الجديد
        $token = Auth::guard($guard)->login($user);
    
        // استجابة مع الـ token وبيانات المستخدم
        return $this->createNewToken($token, 'Registration successful');
    }
    
   
    
    protected function createNewToken($token, $message)
    {
        return response()->json([
            'access_token' => $token,
            'message' => $message,
        
        ]);
    }
    
    
}