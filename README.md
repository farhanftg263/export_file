# export_file[pdf,csv,excel]

Export File can be export the pdf,csv,excel and this only compatible whith cakephp 3.* version.<br>
Impotence of this library is not to use any resource file like HTML to export pdf,csv,excel.<br>

<b>Instalation</b><br>
composer require dakota/cake-excel<br>
composer require tecnickcom/tcpdf


Enable Plugin
Load the plugin in your app's config/bootstrap.php file:<br>
Plugin::load('CakeExcel', ['bootstrap' => true, 'routes' => true]); 


1: Pull export.php file inside the config/ folder of cakephp<br>
2: Import export.php file from bootstrap.php : 
	require_once('export.php')<br>
3:Call from your Controller's action<br>

	$export = new \Export;
        if(in_array($type,[FILE_CSV,FILE_EXCEL]))
        {
            $headerRow = array("S.No","First Name", "Last Name","Created On","Role");
            $export->exportFile($fileName, $headerRow, $data_export,$type);
        }
        
        if($type == FILE_PDF)
        {
            $headerRow = array(
                'id' => array('label' => 'S.No', 'width' => 16),
                'first_name'=> array('label' => 'First Name', 'width' => 40),
                'last_name' => array('label' => 'Last Name', 'width' => 40),
                'created_on' => array('label' => 'Created On', 'width' => 40),
                'role' => array('label' => 'Role', 'width' => 40),
            );
            $export->exportFile($fileName, $headerRow, $data_export,FILE_PDF,'Admin User List');
        }  

