<?php  
define("NO_AGENT_CHECK", true);
define('PUBLIC_AJAX_MODE', true);
define("NO_KEEP_STATISTIC", true);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

$request = Bitrix\Main\Application::getInstance()
		->getContext()
        ->getRequest();

$response = new class(){

    public $error;
    public $success;

    public function out(){
        header('Content-type: application/json');
        echo json_encode($this);
    }
};


if(!empty($request->getPost('add')) && 
    !empty($request->getPost('name')) &&
    !empty($request->getPost('phone')) &&
    Bitrix\Main\Loader::includeSharewareModule("bxup.crmhookclient"))
{   
    $data = [
        'fields' => [
            "OPENED" => "Y",
            "STATUS_ID" => "NEW",
            "NAME" => $request->getPost('name'),
            "SOURCE_ID" => $request->getPost('source_id'),
            "TITLE" => $request->getPost('name').'-'.$request->getPost('phone'),
            "PHONE" => [
                [
                    "VALUE" => $request->getPost('phone'), 
                    "VALUE_TYPE" => "WORK" 
                ]
            ],
            "WEB" => $request->getPost('web'),
            "UTM_TERM" => $request->getPost('utm_term'),
            "UTM_SOURCE" => $request->getPost('utm_source'),
            "UTM_MEDIUM" => $request->getPost('utm_medium'),
            "UTM_CONTENT" => $request->getPost('utm_content'),
            "UTM_COMPAIGN" => $request->getPost('utm_compaign'),
            "UF_CRM_1499161589" => $request->getPost('name').'-'.$request->getPost('phone'),
            
        ],
        'params' => ["REGISTER_SONET_EVENT" => "Y"]
    ];
    
    $result = (new BxUp\CRMHookClient)
	    ->setProfile('crm.lead.add.json')
        ->setData($data)
        ->call(false);

    if(isset($result['result']) && $result['result'] > 0)
        $response->success = true;
}
else
{
    $response->error = 'init params required';
}

$response->out();