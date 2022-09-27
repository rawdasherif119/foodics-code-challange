<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class IngredientReachedBelowItsHalflevel extends Mailable 
{
    use Queueable, SerializesModels;

    protected $ingredient;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($ingredient)
    {
        $this->ingredient = $ingredient;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.ingredient')
            ->subject('Worning : ' . ucWords($this->ingredient->name) . ' Reached below its half level')
            ->with([
                'name' => ucWords($this->ingredient->name),
                'max_amount' => $this->ingredient->max_amount,
                'current_amount' => $this->ingredient->current_amount
            ]);
    }
}
