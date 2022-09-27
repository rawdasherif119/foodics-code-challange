<?php

namespace App\Observers;

use App\Models\Ingredient;
use Illuminate\Support\Facades\Mail;
use App\Mail\IngredientReachedBelowItsHalflevel;

class IngredientObserver
{

    /**
     * Handle the Ingredient "updated" event.
     *
     * @param  \App\Models\Ingredient  $ingredient
     * @return void
     */
    public function updated(Ingredient $ingredient)
    {
        $changedFields = $ingredient->getDirty();
        if (
            array_key_exists('current_amount', $changedFields) &&
            $ingredient->isInHalfOrBelowLevel() &&
            !$ingredient->alert_email_sent
        ) {
            Mail::to(['merchant1@email.com', 'merchant2@gmail'])
                ->send(new IngredientReachedBelowItsHalflevel($ingredient));

            $ingredient->updateAlertEmailSent(true);
        }
    }
}
