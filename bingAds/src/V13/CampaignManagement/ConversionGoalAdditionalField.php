<?php

namespace Microsoft\BingAds\V13\CampaignManagement;

{
    /**
     * Defines a list of optional conversion goal properties that you can request when calling GetConversionGoalsByIds and GetConversionGoalsByTagIds.
     * @link https://docs.microsoft.com/en-us/advertising/campaign-management-service/conversiongoaladditionalfield?view=bingads-13 ConversionGoalAdditionalField Value Set
     * 
     * @used-by GetConversionGoalsByIdsRequest
     * @used-by GetConversionGoalsByTagIdsRequest
     */
    final class ConversionGoalAdditionalField
    {
        /** Request that the ViewThroughConversionWindowInMinutes element be included within each returned ConversionGoal object. */
        const ViewThroughConversionWindowInMinutes = 'ViewThroughConversionWindowInMinutes';
    }

}
