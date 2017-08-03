<?php
namespace App\Library\Results;

use Thread;

class CrawlThread extends Thread
{
    public $id;
    public $session;
    public $semester;
    public $resultCategory;
    public $rollNo;
    public $marksElementId;

    public function __construct($id, $session, $semester, $resultCategory, $rollNo)
    {
        $this->id = $id;
        $this->semester = $semester;
        $this->session = $session;
        $this->resultCategory = $resultCategory;
        $this->rollNo = $rollNo;

        $this->marksElementId = $semester % 2 == 0 ? 'ctl00_ContentPlaceHolder1_emk' : 'ctl00_ContentPlaceHolder1_omk';
    }

    public function run()
    {
        parent::run(); // TODO: Change the autogenerated stub

        require_once(public_path() . \App\Library\ConstantPaths::$PATH_WEB_ASSETS . 'simpletest/browser.php');
        require_once(public_path() . \App\Library\ConstantPaths::$PATH_WEB_ASSETS . 'simplehtmldom/simple_html_dom.php');

        $browser->get('http://bietjhs.ac.in/studentresultdisplay/frmprintreport.aspx');
        $browser = new SimpleBrowser();

        $browser->setFieldById('ctl00_ContentPlaceHolder1_ddlAcademicSession', $this->session);
        $browser->setFieldById('ctl00_ContentPlaceHolder1_ddlSem', $this->semester);
        $browser->setFieldById('ctl00_ContentPlaceHolder1_ddlResultCategory', $this->resultCategory);
        $browser->setFieldById('ctl00_ContentPlaceHolder1_txtRollno', $this->rollNo);

        $browser->click('View');
        $html = new simple_html_dom();

        //die($browser->getContent());
        $html->load($browser->getContent());



        $marks = explode('/', $html->find('#'.$this->marksElementId));


        if (!isset($marks[1]) || !isset($marks[0]))
            return [
                'rollno' => $this->rollNo,
                'name' => 'N/A',
                'percentage' => 'N/A',
            ];

        return [
            'rollno' => $this->rollNo,
            'name' => $html->find('#ctl00_ContentPlaceHolder1_sName',0),
            'percentage' => (floatval($marks[0])) / (floatval($marks[1])),
        ];
    }
}

?>