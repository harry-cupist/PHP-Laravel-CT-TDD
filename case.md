회원가입 유효성 검증 케이스

```php
// try {
//     $request->validate([
//         'email'    => 'required|string|email|max:255|unique:users',
//         'password' => 'required|string|min:6|max:10'
//     ]);
// } catch (Exception $e) {
//     return response()->json([
//         'status' => 'error',
//         'message' => 'validation failed'
//     ], 400);
// }

$validator = Validator::make($request->all(), [
  'email'    => 'required|string|email|max:255|unique:users',
  'password' => 'required|string|min:6|max:10'
]);

if ($validator->fails()) {
  return response()->json([
    'status'   => 'error',
    'messages' => $validator->messages()
  ], 400);
}
```



토큰 갖고오기 

앞의 방법은 왜 안되는걸까?

```php
public function authenticate()
{
  // $user = factory(User::class)->make();
  // $this->user = $user;
  // $token      = JWTAuth::fromUser($user);

  $data = factory(User::class)->make()->toArray();
  $this->post(route('register'), $data);

  $token = $this->post(route('login'), $data)
    ->getData()->access_token;

  return $token;
}
```



TodoController Old version

```php

//
// namespace App\Http\Controllers;
//
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Validator;
// // use Illuminate\Validation\Validator;
// use App\Todo;
// use Tymon\JWTAuth\Facades\JWTAuth;
//
// class TodoController extends Controller
// {
//     /**
//      * @var
//      */
//     protected $user;
//
//     public function __construct()
//     {
//         try {
//             $this->user = JWTAuth::parseToken()->authenticate();
//         } catch (\Exception $e) {
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Unauthorized'
//             ], 401);
//         }
//     }
//
//     /**
//      * @return \Illuminate\Http\JsonResponse
//      */
//     public function index()
//     {
//         $todos = $this->user->todos()->get(['title', 'description', 'completed']);
//
//         return response()->json($todos);
//     }
//
//     /**
//      * @param \Illuminate\Http\Request $request
//      *
//      * @return \Illuminate\Http\JsonResponse
//      */
//     public function store(Request $request)
//     {
//         $validator = Validator::make($request->all(), $this->validateRequest());
//
//         if ($validator->fails()) {
//             return $this->validateBadResponse($validator);
//         }
//
//         $todo              = new Todo;
//         $todo->title       = $request->title;
//         $todo->description = $request->description;
//
//         if ($this->user->todos()->save($todo)) {
//             return response()->json([
//                 'success' => true,
//                 'message' => 'New todo is successfully created',
//                 'data' => $todo->toArray()
//             ], 201);
//         }
//
//         return response()->json([
//             'success' => false,
//             'message' => 'New todo could not be created'
//         ], 500);
//     }
//
//     /**
//      * @param int $id
//      *
//      * @return \Illuminate\Http\JsonResponse
//      */
//     public function show($id)
//     {
//         $todo = $this->user->todos()->find($id);
//
//         if (!$todo) {
//             return $this->respondNotFound($id);
//         }
//
//         return response()->json([
//             'success' => true,
//             'data'    => $todo,
//         ], 200);
//     }
//
//     /**
//      * @param \Illuminate\Http\Request $request
//      * @param int $id
//      *
//      * @return \Illuminate\Http\JsonResponse
//      */
//     public function update(Request $request, $id)
//     {
//         $todo = $this->user->todos()->find($id);
//
//         if (!$todo) {
//             return $this->respondNotFound($id);
//         }
//
//         $validator = Validator::make(
//             $request->all(),
//             array_merge($this->validateRequest(), ['completed' => 'boolean'])
//         );
//
//         // $validator = Validator::make($request->all(), [
//         //     'title'       => 'string|min:3',
//         //     'description' => 'string|min:3',
//         //     'completed'   => 'boolean'
//         // ]);
//
//         if ($validator->fails()) {
//             return $this->validateBadResponse($validator);
//         }
//
//         $todo->completed = $request->completed;
//         $newTodo         = $todo->fill($request->all())->save();
//
//         if (!$newTodo) {
//             return response()->json([
//                 'success' => false,
//                 'message' => 'The todo could not be updated'
//             ], 500);
//         }
//
//         return response()->json([
//             'success' => true,
//             'message' => 'The todo was successfully updated',
//         ], 200);
//     }
//
//     /**
//      * @param int $id
//      *
//      * @return \Illuminate\Http\JsonResponse
//      */
//     public function destroy($id)
//     {
//         $todo = $this->user->todos()->find($id);
//
//         if (!$todo) {
//             return $this->respondNotFound($id);
//         }
//
//         if (!$todo->delete()) {
//             return response()->json([
//                 'success' => false,
//                 'message' => 'The todo could not be deleted'
//             ], 500);
//         }
//
//         return response()->json([
//             'success' => true,
//             'message' => 'successfully deleted',
//             'data' => $todo
//         ], 200);
//     }
//
//     /**
//      * return data for validation
//      *
//      * @return array
//      */
//     private function validateRequest()
//     {
//         return [
//             'title'       => 'required|string|min:3',
//             'description' => 'required|string|min:3',
//         ];
//     }
//
//     /**
//      * return 400 error response
//      *
//      * @param mixed $validator
//      *
//      * @return \Illuminate\Http\JsonResponse
//      */
//     private function validateBadResponse($validator)
//     {
//         return response()->json([
//             'success' => false,
//             'message' => $validator->messages()
//         ], 400);
//     }
//
//     /**
//      * return 404 error response
//      *
//      * @param $id
//      *
//      * @return \Illuminate\Http\JsonResponse
//      */
//     private function respondNotFound($id)
//     {
//         return response()->json([
//             'success' => false,
//             'message' => 'todo for id ' . $id . ' does not exist.'
//         ], 404);
//     }
// }

```



AuthController 구버젼

```php
<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|max:10'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'   => 'error',
                'messages' => $validator->messages()
            ], 400);
        }

        $user           = new User;
        $user->email    = $request->email;
        $user->password = bcrypt($request->password);
        $user->save();

        return response()->json([
            'success' => true,
            'data'    => $user,
            'message' => 'Successfully registered',
        ], 201);
    }

    /**
     * Get a JWT via given credentials
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $credentials = request(['email', 'password']);

        if (!$token = auth()->attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Get the authenticated User
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function user()
    {
        return response()->json([
            'success' => true,
            'data'    => auth()->user()
        ], 200);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json([
            'success' => true,
            'message' => 'Successfully logged out'
        ], 200);
    }

    /**
     * Get the token array structure.
     *
     * @param string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => auth()->factory()->getTTL() * 60
        ]);
    }

    /**
     * return data for validation
     *
     * @return array
     */
    private function validateRequest()
    {
        return [
            'title'       => 'required|string|min:3',
            'description' => 'required|string|min:3',
        ];
    }

    /**
     * return 400 error response when input data validation failed.
     *
     * @param mixed $validator
     *
     * @return \Illuminate\Http\JsonResponse
     */
    private function validateBadResponse($validator)
    {
        return response()->json([
            'success' => false,
            'message' => $validator->messages()
        ], 400);
    }

    /**
     * return adequate Http Status code for error
     *
     * @param string $message
     * @param int $status
     *
     * @return \Illuminate\Http\JsonResponse
     */
    private function respondWithError(string $message, int $status)
    {
        return response()->json([
            'success' => false,
            'message' => $message . ' failed',
        ], $status);
    }

    /**
     * return adequate Http Status code success
     *
     * @param string $message
     * @param int $status
     * @param object $data
     *
     * @return \Illuminate\Http\JsonResponse
     */
    private function respondWithSuccess(string $message, int $status, object $data)
    {
        return response()->json([
            'success' => true,
            'message' => $message . ' succeed',
            'data' => $data->toArray()
        ], $status);
    }
}

```

