<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Filter;

class FilterController extends Controller {
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function add(Request $request) {
        $this->validate($request, [
            'name' => 'required'
        ]);

        if(empty($request->all('filter')['filter']) && $request->all('searchTerm')['searchTerm'] == '') {
            return json_encode(['Status' => 'Error', 'Message' => 'A search term must be entered or one or more filters must be selected']);
        }

        $user = Auth::id();

        $filter = new Filter;
        
        // Check for exact copy of filter
        $existingFilter = $filter->where('userId', '=', $user)->where('filter', '=', json_encode($request->all('filter')['filter']))->where('searchTerm', '=', $request->all('searchTerm')['searchTerm'])->first();
        if ($existingFilter !== null) {
            return json_encode(['Status' => 'Error', 'Message' => 'This filter already exists']);
        }

        // Check for that name already being used for this user
        $existingFilter = $filter->where('userId', '=', $user)->where('name', '=', $request->all('name')['name'])->first();
        if ($existingFilter !== null) {
            return json_encode(['Status' => 'Error', 'Message' => 'This name already exists']);
        }

        $filter->userId = $user;
        $filter->name = $request->all('name')['name'];
        $filter->filter = json_encode($request->all('filter')['filter']);
        $filter->searchTerm = $request->all('searchTerm')['searchTerm'];

        if($filter->save()) {
            $response = json_encode(['Status' => 'Success', 'Message' => 'Filter Created Successfully']);
        } else {
            $response = json_encode(['Status' => 'Error', 'Message' => 'An Error has Occurred']);
        }

        return $response;
    }

        /**
     * Return all games for a user.
     *
     * @return \Illuminate\Http\Response
     */
    public function list() {
        if (!Auth::check()) {
            return json_encode(['Status'=>'Error','Message'=>'User Not Logged In', 'Logout'=> true]);
        }

        $user = Auth::id();

        $model = new Filter;
        $filters = $model->filterList($user);

        if(count($filters) > 0) {
            $response['Filters'] = $filters;
            $response['Status'] = 'Success';
        } else {
            $response['Status'] = 'Error';
            $response['Message'] = 'No Filters Found';
        }

        return json_encode($response);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function apply(Request $request) {
        if (!Auth::check()) {
            return json_encode(['Status'=>'Error','Message'=>'User Not Logged In', 'Logout'=> true]);
        }

        $user = Auth::id();
        $id = $request->all('id')['id'];

        $filter = Filter::find($id);

        // If the Filter does not belong to this user
        if( $filter && $user == $filter['userId'] ) {
            $response['Filter'] = $filter;
            $response['Status'] = 'Success';
        } else { 
            $response['Status'] = 'Error';
            $response['Message'] ='Filter Not Found';
        }

        return json_encode($response);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateFilter(Request $request) {
        if (!Auth::check()) {
            return json_encode(['Status'=>'Error','Message'=>'User Not Logged In', 'Logout'=> true]);
        }

        $user = Auth::id();
        $id = $request->all('id')['id'];

        $filter = Filter::find($id);
        // Verify filter belogs to the user
        if( $filter && $user != $filter['userId'] ){
            return json_encode(['Status' => 'Error', 'Message' => 'Invalid Filter']);
        }

        $filter->filter = $request->all('filter')['filter'];
        $filter->searchTerm = $request->all('searchTerm')['searchTerm'];

        if($filter->save()) {
            $result = json_encode(['Status' => 'Success', 'Message' => 'Filter Updated Successfully']);
        } else {
            $result = json_encode(['Status' => 'Error', 'Message' => 'An Error has Occurred']);
        }

        return $result;
    }

    public function updateName(Request $request) {
        if (!Auth::check()) {
            return json_encode(['Status'=>'Error','Message'=>'User Not Logged In', 'Logout'=> true]);
        }

        $user = Auth::id();
        $id = $request->all('id')['id'];

        $filter = Filter::find($id);
        // Check for that name already being used for this user
        $existingFilter = $filter->where('userId', '=', $user)->where('name', '=', $request->all('name')['name'])->first();
        if ($existingFilter !== null) {
            return json_encode(['Status' => 'Error', 'Message' => 'This name already exists']);
        }
        // Verify filter belogs to the user
        if( $filter && $user != $filter['userId'] ){
            return json_encode(['Status' => 'Error', 'Message' => 'Invalid Filter']);
        }

        $filter->name = $request->all('name')['name'];

        if($filter->save()) {
            $result = json_encode(['Status' => 'Success', 'Message' => 'Filter Updated Successfully']);
        } else {
            $result = json_encode(['Status' => 'Error', 'Message' => 'An Error has Occurred']);
        }

        return $result;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request) {
        if (!Auth::check()) {
            return json_encode(['Status'=>'Error','Message'=>'User Not Logged In', 'Logout'=> true]);
        }

        $user = Auth::id();
        $id = $request->all('id')['id'];

        $filter = Filter::find($id);
        // Verify filter belogs to the user
        if( $filter && $user != $filter['userId'] ){
            return json_encode(['Status' => 'Error', 'Message' => 'Invalid Filter']);
        }

        if($filter->delete()) {
            $result = json_encode(['Status' => 'Success', 'Message' => 'Filter Deleted Successfully']);
        } else {
            $result = json_encode(['Status' => 'Error', 'Message' => 'An Error has Occurred']);
        }

        return $result;
    }
}
