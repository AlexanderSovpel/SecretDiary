<?php
session_start();

class PasswordRestoration {
    const subject = "SDiary password restore";
    const from = "From: SDiary Support Team <contact@sdiary.pe.hu>";
    const greetings = "Здравствуйте. Вы запросили восстановление пароля на сайте SDiary.\n";
    const toRestoreUseCode = "Для восстановления, пожалуйста, используйте следующий код:\n";
    const ifNotYou = "\nЕсли это были не вы, просто проигнорируйте данное сообщение. Спасибо.";

    private $to;
    private $restoreCode;

    function __construct($to) {
        $this->to = $to;
        $_SESSION['restore_email'] = $to;

        $this->restoreCode = md5($to . time());
        $_SESSION['restore_code'] = $this->restoreCode;

        $message = $this->greetings . $this->toRestoreUseCode . $this->restoreCode . $this->ifNotYou;
        mail($this->to, $this->subject, $message, $this->from);
    }
}
?>