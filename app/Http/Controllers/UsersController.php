<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;


class UsersController extends Controller{
    public function __construct()
    {
        $this->userData = [
            [
                "id" => 1,
                "name" => "Sumatrana",
                "email" => "sumatrana@gmail.com",
                "address" => "padang",
                "gender" => "Laki-laki",
            ],[
                "id" => 2,
                "name" => "Jawarianto",
                "email" => "jawarianto@gmail.com",
                "address" => "Cimahi",
                "gender" => "Laki-laki"
            ],[
                "id" => 3,
                "name" => "Kalimantanio",
                "email" => "kalimantanio@gmail.com",
                "address" => "Samarinda",
                "gender" => "Laki-laki"
            ],[
                "id" => 4,
                "name" => "Sulawesiani",
                "email" => "sulawesiani@gmail.com",
                "address" => "Makasar",
                "gender" => "Perempuan"
            ],[
                "id" => 5,
                "name" => "Papuani",
                "email" => "papuani@gmail.com",
                "address" => "Jayapura",
                "gender" => "Perempuan"
            ],
        ];
    }

    private function findUserIndexById($userId)
    {
        foreach ($this->userData as $index => $data) {
            if ($data['id'] == $userId) {
                return $index;
            }
        }
        return false;
    }


    public function index()
    {
       $data = $this->userData;

       return response()->json($data);
    }

    public function show($userId){
        foreach ($this->userData as $user) {
            if ($user['id'] == $userId) {
                return response()->json($user);
            }
            return response()->json(['error' => 'User Tidak Ditemukan'], 404);
        }

    }

    public function store(Request $request){
        $post = [
            'id' => count($this->userData) + 1,
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'address' => $request->input('address'),
            'gender' => $request->input('gender'),
        ];

        $this->userData[] = $post;

        return response()->json($post, 201, ['massage' => 'Data Berhasil Ditambahkan']);
    }

    public function update(Request $request, $userId)
    {
        $index = $this->findUserIndexById($userId);

        if ($index !== false) {
            $this->userData[$index]['name'] = $request->input('name');
            $this->userData[$index]['email'] = $request->input('email');
            $this->userData[$index]['address'] = $request->input('address');
            $this->userData[$index]['gender'] = $request->input('gender');
            $this->userData[$index]['id'] = $userId;

            return response()->json($this->userData[$index], 200, ['message' => 'Data Berhasil Diubah']);
        } else {
            return response()->json(['error' => 'User Tidak Ditemukan'], 404);
        }
    }

    public function patch(Request $request, $userId, $resource){
        if ($resource === 'name' || $resource === 'email' || $resource === 'address' || $resource === 'gender') {
            $index = $this->findUserIndexById($userId);
            if ($index !== false) {
                $this->userData[$index][$resource] = $request->input($resource);
                return response()->json($this->userData[$index], 200, ['message' => 'Data Berhasil Diubah']);
            }else{
                return response()->json(['error' => 'User Tidak Ditemukan'], 404);
            }
        }else{
            return response()->json(['error' => 'Resource Tidak Ditemukan'], 404);
        }
    }

    public function delete($userId)
    {
        $index = $this->findUserIndexById($userId);

        if ($index !== false) {
            array_splice($this->userData, $index, 1);

            return response()->json(['message' => 'Data dengan id '. $userId . ' berhasil dihapus'], 200);
        } else {
            return response()->json(['error' => 'User tidak ditemukan'], 404);
        }
    }

}
