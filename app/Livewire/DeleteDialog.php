<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;

class DeleteDialog extends Component
{
    public $isOpen = false;
    public $title = 'Confirm Deletion';
    public $message = 'Are you sure you want to delete this item?';
    public $itemName = '';
    public $confirmText = 'Delete';
    public $cancelText = 'Cancel';
    public $formAction = '';

    #[On('open-delete-dialog')]
    public function open(
        $title = null,
        $message = null,
        $itemName = null,
        $confirmText = null,
        $cancelText = null,
        $formAction = null
    ) {
        if (is_array($title)) {
            $params = $title;
            $title = $params['title'] ?? null;
            $message = $params['message'] ?? null;
            $itemName = $params['itemName'] ?? null;
            $confirmText = $params['confirmText'] ?? null;
            $cancelText = $params['cancelText'] ?? null;
            $formAction = $params['formAction'] ?? null;
        }

        $this->title = $title ?? 'Confirm Deletion';
        $this->message = $message ?? 'Are you sure you want to delete this item?';
        $this->itemName = $itemName ?? '';
        $this->confirmText = $confirmText ?? 'Delete';
        $this->cancelText = $cancelText ?? 'Cancel';
        $this->formAction = $formAction ?? '';

        $this->isOpen = true;
    }

    public function close()
    {
        $this->isOpen = false;
    }

    public function render()
    {
        return view('livewire.delete-dialog');
    }
}