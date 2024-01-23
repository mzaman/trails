<?php

namespace MasudZaman\Trails\Constants;

/**
 * Class ActionTypes
 *
 * Constants representing various action types for user interactions.
 * Each constant provides a high-level classification of different actions.
 *
 * @package MasudZaman\Trails\Constants\Constants
 */
class ActionTypes
{
    // User Interactions
    const CLICK = 'click';  // Represents a user click on an element.
    const VIEW = 'pageview';    // Represents a user viewing content or a page.
    const CONVERSION = 'conversion';  // Represents a conversion action, e.g., completing a purchase.
    const RATING = 'rating';  // Represents a user providing a rating.
    const SUBMISSION = 'submission';  // Represents a form submission.
    const DOWNLOAD = 'download';  // Represents a download action.
    const INTERACTION = 'interaction';  // Represents a generic user interaction.
    const ENGAGEMENT = 'engagement';  // Represents user engagement.
    const IMPRESSION = 'impression';  // Represents an impression or display of content.
    
    // E-commerce Actions
    const CHECKOUT = 'checkout';  // Represents a checkout action.
    const PURCHASE = 'purchase';  // Represents a purchase transaction.
    
    // User Account Actions
    const SIGNUP = 'signup';  // Represents a user sign-up action.
    const LOGIN = 'login';  // Represents a user login action.


    /**
     * Get all constants as an array.
     *
     * @return array
     */
    public static function all()
    {
        return (new \ReflectionClass(self::class))->getConstants();
    }
}
