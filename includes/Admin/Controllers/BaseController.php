<?php
namespace Pfs\Admin\Controllers;
abstract class BaseController {
    protected $message = [];
    protected function register_route() {}
    protected function loadViews($views, $title, array $data = []) {
        echo '<div class="wrap">';
        if ($this->hasError()) {
            echo '<div class="error">';
            foreach ($this->getErrorList() as $error) {
                echo "<p>{$error['message']}</p>";
            }
            echo '</div>';
        }
        if ($this->hasSucces()) {
            echo '<div class="updated">';
            foreach ($this->getSuccesList() as $succes) {
                echo "<p>{$succes['message']}</p>";
            }
            echo '</div>';
        }
        echo "<h1>{$title}</h1>";

        extract($data);

        require PFS_ADMIN_VIEWS . $views;

        echo '</div>';
    }
    protected function addMessage($status, $code, $message) {
        $this->message[] = [
            'status' => $status,
            'code' => $code,
            'message' => $message,
        ];
    }

    protected function addError($code, $message) {
        $this->addMessage('error', $code, $message);
    }

    protected function addSucces($code, $message) {
        $this->addMessage('succes', $code, $message);
    }

    protected function getMessageListByStatus($status) {
        $filtered = [];
        foreach ($this->message as $message) {
            if ($message['status'] === $status) {
                $filtered[] = $message;
            }
        }
        return $filtered;
    }

    protected function getErrorList() {
        return $this->getMessageListByStatus('error');
    }

    protected function getSuccesList() {
        return $this->getMessageListByStatus('succes');
    }

    private function hasMessage($status) {
        return !empty($this->getMessageListByStatus($status));
    }

    protected function hasError() {
        return $this->hasMessage('error');
    }

    protected function hasSucces() {
        return $this->hasMessage('succes');
    }

}
