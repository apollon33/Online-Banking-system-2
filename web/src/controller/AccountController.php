<?php
namespace agilman\a2\controller;

use agilman\a2\model\{
    BankAccountModel, UserAccountModel
};
use agilman\a2\view\View;

/**
 * Class AccountController
 *
 * @package agilman/a2
 * @author  Andrew Gilman <a.gilman@massey.ac.nz>
 */
class AccountController extends Controller
{
    /**
     * Account Index action
     */
    public function showAccounts()
    {
        $user = UserAccountController::getCurrentUser();
        if($user !== null) {
            $accounts = $user->getBankAccounts();
            $view = new View('userHome');
            $view->addData('userName', $_SESSION['userName']);
            $view->addData('accounts', $accounts);
            echo $view->render();
        }
    }
    /**
     * Account Create action
     */
    public function createAction()
    {
        $user = UserAccountController::getCurrentUser();
        if($user === null) {
            return;
        }
        $account = new BankAccountModel();
        $names = ['Bob','Mary','Jon','Peter','Grace'];
        shuffle($names);
        $account->setName($names[0]); // will come from Form data
        $account->setBalance(0);
        $account->setUserId($user->getID());
        $account->save();
        $id = $account->getId();
        $view = new View('accountCreated');
        $view->addData('accountId', $id);
        echo $view->render();
    }

    /**
     * Account Delete action
     *
     * @param int $id Account id to be deleted
     */
    public function deleteAction($id)
    {
        $user = UserAccountController::getCurrentUser();
        if($user == null) {
            return;
        }
        $bankAccount = $user->getBankAccountByID($id);
        if($bankAccount !== null) {
            $bankAccount->delete();
            $view = new View('accountClosed');
            $view->addData('deleted', true);
            echo $view->addData('accountId', $id)->render();
        } else {
            $view = new View('accountClosed');
            $view->addData('deleted', false);
            echo $view->addData('accountId', $id)->render();
        }
    }
    /**
     * Account Update action
     *
     * @param int $id Account id to be updated
     */
    public function updateAction($id)
    {
        $account = (new BankAccountModel())->load($id);
        $account->setName('Joe')->save(); // new name will come from Form data
    }

    /**
     *  Account to create deposit page
     * @param int $id Account id to be deposited
     */
    public function createDepositPage($id){
        $user = UserAccountController::getCurrentUser();
        if($user == null) {
            return;
        }
        $bankAccount = $user->getBankAccountByID($id);
        if($bankAccount !== null) {
            $view = new View('transDeposit');
            $view->addData('accountId', $id);
            echo $view->render();
        }
    }

    /**
     *  This function do the deposit, it will first check correct user information
     *  then check account existence
     */
    public function deposit($id){
        $user = UserAccountController::getCurrentUser();
        if($user == null) {
            //incorrect user information
            return;
        }
        $bankAccount = $user->getBankAccountByID($id);
        if($bankAccount !== null) {
            //correct user information and account number
            $balance = $bankAccount->getBalance() + $_POST['amount'];
            $bankAccount->setBalance($balance);
            $bankAccount->save();
            $view = new View("transDeposit");
            $message = "Deposit amount : " .  $_POST['amount'] . "\n";
            $message = $message . "Balance for $id : " . $bankAccount->getBalance();
            $view->addData("message", $message)->render();
        } else {

        }

    }
}

