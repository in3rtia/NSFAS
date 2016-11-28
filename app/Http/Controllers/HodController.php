<?php

namespace App\Http\Controllers;


use App\Accounts;
use App\Budget;
use App\BudgetItems;
use App\CalculatedTotal;
use App\Departments;
use App\Expenditure;
use App\Income;
use App\Projects;
use App\User;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;

use Auth;
use Validator;
use Mail;
use Hash;

class HODController extends Controller
{

    protected $totalCost;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
       return view('hod.addStaff');
    }

    public function staff(){
        $id = $this->getDepartmentIdFromLoggedInUSer() ;
        $staff = User::where('departments_id', $id)->get();
        if($staff){
            return view('hod.staff')->with('staff', $staff);
        }else{
            return view('hod.staff');
        }
    }

 //
    public function imprestForm(){

        return view('imprests.imprests');
    }

    public function addStaff(Request $request){
        $validator = $this->addStaffValidation($request->all());
        if ($validator->fails()) {
            $this->throwValidationException(
                $request, $validator
            );
        }

        $password = str_random(8);

        $staff = new User();
        $staff->manNumber = $request['manNumber'];
        $staff->email = $request['email'];
        $staff->access_level_id= 'OT';
        $staff->password = bcrypt($password);
        $staff->lastName = ucfirst($request['lastName']);
        $staff->firstName = ucfirst($request['firstName']);
        $staff->otherName = ucfirst($request['otherName']);
        $staff->phoneNumber= $request['phoneNumber'];
        $staff->departments_id= $this->getDepartmentIdFromLoggedInUSer();
        $staff->save();

        // code for sending email to the added user goes here

        Session::flash('flash_message', 'staff added successfully! '. $password);
        Return Redirect::action('HodController@staff');
    }

    public function projectInfo(){
        if ($this->getAccessLevelId() == 'OT'){
            $record = Projects::where('departments_id', $this->getDepartmentIdFromLoggedInUSer())
                ->where('projectCoordinator', $this->getUsersFullName())
                ->get();
        }else{
            $record = Projects::where('departments_id', $this->getDepartmentIdFromLoggedInUSer())->get();
        }

        foreach ($record as $rec){
            $this->projectTotalBudgetCalculator($rec->id);
            $this->projectTotalIncomeCalculator($rec->id);
        }

        if($record){
            return view('hod.projectInfo')->with('record', $record);
        }else{
            return view('hod.projectInfo');
        }
    }

    public function requestForMoney($id){
        $projects = Projects::find($id);
        $budget = Budget::where('projects_id', $id)->first();
        $budget = BudgetItems::where('budget_id', $budget->id)->get();
        return view('hod.projectMoneyRequest')
            ->with('projects', $projects)
            ->with('budget', $budget);
    }

    public function projectMoneyRequest(Request $request,$id){
        $validator = $this->projectMoneyRequestValidation($request->all());
        if ($validator->fails()) {
            $this->throwValidationException(
                $request, $validator
            );
        }

        $account = Accounts::where('projects_id', $id)->first();
        $project = Projects::where('id', $id)->first();
        $expend = new Expenditure();
        $expend->amountPaid = $request['requestedAmount'];
        $expend->budgetLine = $request['budgetLine'];
        $expend->beneficiary= $request['beneficiary'];
        $expend->purpose = $request['purpose'];
        $expend->datePaid = $request['date'];
        $expend->accounts_id = $account->id;
        $project->expenditures()->save($expend);

        Session::flash('flash_message', 'Your request has been successfully submitted!');
        Return Redirect::action('HodController@projectExpenditures');
    }

    public function requestApproval($id){

        $projects = Projects::where('id',$id)->first();
        if ($projects->expenditures->approvedByHOD == 0){
            $projects->expenditures->approvedByHOD = 1;
            $projects->save();

            $message = " success";
        }else{
            $message = 'error';
        }
        Session::flash('flash_message', $message);
        Return Redirect::back();
    }
    public function projectExpenditures(){
        if ($this->getAccessLevelId() == 'OT'){
            $projects = Projects::where('departments_id', $this->getDepartmentIdFromLoggedInUSer())
                ->where('projectCoordinator', $this->getUsersFullName())
                ->get();
        }else{
            $projects= Projects::where('departments_id', $this->getDepartmentIdFromLoggedInUSer())->get();
        }
        if ($projects){
            return view('hod.projectExpenditures')->with('projects', $projects);
        }
    }

    public function addProject(){

       $id = $this->getDepartmentIdFromLoggedInUSer();
        $staff = User::where('departments_id', $id)->get();
        if ($this->getAccessLevelId() == 'OT'){
            $projects = Projects::where('departments_id', $this->getDepartmentIdFromLoggedInUSer())
                ->where('projectCoordinator', $this->getUsersFullName())
                ->orderBy('created_at', 'desc')
                ->take(2)
                ->get();
        }else{
            $projects = Projects::where('departments_id', $id)
                ->orderBy('created_at', 'desc')
                ->take(2)
                ->get();
        }
        if($staff){
            return view('hod.addProjects')->with('staff', $staff)->with('projects', $projects);
        }else{
            return view('hod.addProjects');
        }
    }

    public function saveProject(Request $request){
        $validator = $this->saveProjectValidation($request->all());
        if ($validator->fails()) {
            $this->throwValidationException(
                $request, $validator
            );
        }

        $net_project_budget = $request['netProjectBudget'];
        $departmentPercentage = $request['departmentPercentage'];
        $unzaPercentage = $request['unzaPercentage'];
        $actualProjectBudget= $request['actualProjectBudget'];

         $dPercentage = ($departmentPercentage/100) * $net_project_budget;
         $uPercentage = ($unzaPercentage/100) * $net_project_budget;

        $id = $this->getDepartmentIdFromLoggedInUSer();
        $department = Departments::where('id', $id)->first();
        $newProject = new Projects();
        $newProject->projectName = ucfirst($request['projectName']);
        $newProject->projectCoordinator = $request['projectCoordinator'];
        $newProject->description = $request['description'];
        $newProject->startDate= $request['startDate'];
        $newProject->endingDate = $request['endDate'];

        if ($department->projects()->save($newProject)){
            $projectName =  $request['projectName'];
            $record = Projects::where('projectName', $projectName)->first();
            if (isset($record)){
                $budget = new Budget();

                $budget->budgetName = $projectName;
                $budget->netProjectBudget = $request['netProjectBudget'];
                $budget->departmentAmount = $dPercentage;
                $budget->unzaAmount = $uPercentage;
                $budget->actualProjectBudget = $actualProjectBudget;

                if ($record->budget()->save($budget)){
                    $account = new Accounts();
                    $account->accountName = $projectName;
                    $record->accounts()->save($account);
                }
            }else{

            }
        }else{

        }
        Session::flash('flash_message', 'The '.$projectName.' project has been added successfully!outline your budget and await for the HD approval!');
        Return Redirect::action('HodController@addProject');
    }

    public function projectBudget($id){
        $this->projectTotalIncomeCalculator($id);
        $this->projectTotalBudgetCalculator($id);
        $records = Projects::where('id',$id)->first();
        $total = CalculatedTotal::where('projects_id', $id)->first();
        $data = Budget::where('projects_id', $records->id)->first();
        $items = BudgetItems::where('budget_id', $data->id)
            ->orderBy('created_at', 'desc')
            ->get();
        return view('hod.projectBudgeting')
            ->with('records', $records)
            ->with('items', $items)
            ->with('total', $total);
    }

    public function projectTotalIncomeCalculator($projects_id){
        $project = Projects::where('id', $projects_id)->first();
        $account = Accounts::where('projects_id', $projects_id)->first();
        $totalAmountReceived = Income::where('accounts_id',$account->id)->sum('amountReceived');
        $record = CalculatedTotal::where('projects_id', $projects_id)->first();
        if (isset($record)){
            $record->incomeAcquired  = $totalAmountReceived;
            $project->totalAmount()->save($record);
        }else{
            $total = new CalculatedTotal();
            $total->incomeAcquired = $totalAmountReceived;
            $project->totalAmount()->save($total);
        }
    }

    public function projectTotalBudgetCalculator($id){
        $project = Projects::where('id', $id)->first();
        $budget = Budget::where('projects_id', $id)->first();
        $totalBudget= BudgetItems::where('budget_id', $budget->id)->sum('cost');
        $record = CalculatedTotal::where('projects_id', $id)->first();
        if (isset($record)){
            $record->proposedBudget = $totalBudget;
            $project->totalAmount()->save($record);
        }else{
            $total = new CalculatedTotal();
            $total->proposedBudget = $totalBudget;
            $project->totalAmount()->save($total);
        }

        return $totalBudget;
    }


    public function saveProjectBudget(Request $request, $id){
        $validator = $this->projectBudgetValidation($request->all());
        if ($validator->fails()) {
            $this->throwValidationException(
                $request, $validator
            );
        }

        $record = Budget::where('projects_id',$id)->first();
        $actualProjectBudget = $record->actualProjectBudget;
        if (($this->projectTotalBudgetCalculator($id) + $request['cost']) <= $actualProjectBudget){
            if (isset($record)){

                $info = new BudgetItems();
                $info->budgetLine = $request['budgetLine'];
                $info->cost = $request['cost'];
                $info->description = $request['description'];
                $info->quantity = $request['quantity'];
                $info->pricePerUnit= $request['costPerUnit'];
                $record->budgetItems()->save($info);

                $this->projectTotalBudgetCalculator($id);

                if ($this->projectTotalBudgetCalculator($id)==$actualProjectBudget){
                    //send an email to  the Hod so that he can approve the project's budget
                }

            }else{
                //display an error message
                Session::flash('flash_message', 'Error!');
                Return Redirect::back();
            }

        }else{
            //display an error message
            Session::flash('flash_message', 'Sorry make sure that your total budget does not exceed K'.$actualProjectBudget.'.00');
            Return Redirect::back();
        }

        Session::flash('flash_message', 'ok!');
        Return Redirect::action('HodController@projectBudget', $id);
    }



    public function viewBudget(){
        return view('hod.budgetInfo');
    }

    public function getDepartmentIdFromLoggedInUSer(){
        $user = Auth::user();
        $id = $user->departments_id;
        return $id;
    }

    public function getAccessLevelId(){
        $user = Auth::user();
        $access_level_id = $user->access_level_id;
        return  $access_level_id;
    }

    public function getUsersFullName(){
        $user = Auth::user();
        $name = $user->firstName.' '.$user->otherName.' '.$user->lastName;
        return $name;
    }

    /**
     * Get a validator for an incoming profile editing request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function projectBudgetValidation(array $data){
        return Validator::make($data, [
            'cost' => 'required|max:150',
            'quantity' => 'required|max:150',
            'costPerUnit' => 'required|max:150',
            'description' => 'required|max:255',
            'budgetLine' => 'required|max:255',
        ]);
    }

    /**
     * @param array $data
     * @return mixed
     */
    protected function projectMoneyRequestValidation(array $data){
        return validator::make($data, [
            'date' => 'required|max:255',
            'beneficiary' => 'required|max:255',
            'purpose' => 'required|max:255',
            'requestedAmount' => 'required|max:255',
            'budgetLine' => 'required|max:255',
        ]);
    }
    /**
     * Get a validator for an incoming profile editing request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function saveProjectValidation(array $data){
        return Validator::make($data, [
            'projectName' => 'required|max:255|unique:projects',
            'projectCoordinator' => 'required|max:255',
            'startDate' => 'required|max:255',
            'endDate' => 'required|max:255',
        ]);
    }

    /**
     * Get a validator for an incoming profile editing request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function addStaffValidation(array $data)
    {
        return Validator::make($data, [
            'lastName' => 'required|max:40',
            'firstName' => 'required|max:40',
            'otherName' => '|max:40',
            'manNumber' => 'required|max:40|unique:users',
            'email' => 'required|max:255|unique:users',
            'phoneNumber' => 'required|max:255|unique:users',
        ]);
    }
}