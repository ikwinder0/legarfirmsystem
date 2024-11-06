<?php
/**
 * Class AppHelper
 * @author ningmar
 * @package App\Helpers
 */

namespace App\Helpers;


class AppHelper
{
    public function getFileType($file_url) {
        $file_detail = explode('.',$file_url);
        //check if image
        $image_extensions = ['png','jpg','jpeg','PNG','JPG','JPEG'];
        $pdf_extensions = ['pdf','PDF'];
        $doc_extensions = ['doc','docx','DOC','DOCX'];
        $excel_extensions = ['xls','xlxs','XLS','XLXS','xlsx','XLSX'];
        $_type = '<i class="las la-file-alt" style="font-size: 3em"></i>';
        if(in_array($file_detail[1],$image_extensions)){
            $_type = 'image';
        } elseif(in_array($file_detail[1],$pdf_extensions)) {
            $_type = '<i class="lar la-file-pdf" style="font-size: 3em"></i>';
        } elseif(in_array($file_detail[1],$excel_extensions)) {
            $_type = '<i class="las la-file-excel" style="font-size: 3em"></i>';
        } elseif(in_array($file_detail[1],$doc_extensions)) {
            $_type = '<i class="las la-file-alt" style="font-size: 3em"></i>';
        }
        return $_type;
    }
}