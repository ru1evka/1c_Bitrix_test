<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Новости банка");
?>

<?php
echo '<pre>';
if (($file = fopen('test.csv', 'r')) !== false) {
    $counter = 0;
    $keys = array();
    $result = array();
    while (($data = fgets($file)) !== false) {
        $counter++;
        $data = str_replace(array("\r\n", "\r", "\n"), '', $data);
        $str = explode(';', $data);
        if ($counter == 1) {
            $keys = $str;
        } else {
            $el = array();
            foreach ($str as $key => $item) {
                $el[$keys[$key]] = $item;
            }
            $result[] = $el;
        }
    }
}

if (!feof($file)) {
    echo "Error: unexpected fgets() fail\n";
}
var_dump($result);
fclose($file);

CModule::IncludeModule('iblock');
$bs = new CIBlockElement;
foreach ($result as $key => $value) {
    $Element = CIBlockElement::GetList([], ['IBLOCK_ID' => 3, 'XML_ID' => $value['id']], false, false, ["ID"]);
    if (!$Element->Fetch()) {
        $PROP = [];
        $PROP['NAME'] = $value['name'];
        $PROP['PREVIEW'] = $value['preview_text'];
        $PROP['DETAIL'] = $value['detail_text'];
        $PROP['PROP1'] = $value['prop1'];
        $PROP['PROP2'] = $value['prop2'];

        $arrayFields = [
            'ACTIVE' => 'Y',
            'IBLOCK_ID' => 3,
            'NAME' => $value['id'],
            'XML_ID' => $value['id'],
            'PROPERTY_VALUES' => $PROP,
        ];
        if ($PRODUCT_ID = $bs->Add($arrayFields)) {
            echo $key . '.New ID: ' . $PRODUCT_ID . '(XML_ID = ' . $value['id'] . ')<br>';
        } else {
            echo $key . '.Error: ' . $bs->LAST_ERROR . '<br>';
        }
    }
}

?>

<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>