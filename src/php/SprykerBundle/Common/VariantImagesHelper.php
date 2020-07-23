<?php

namespace Frontastic\Common\SprykerBundle\Common;

use Frontastic\Common\SprykerBundle\Domain\Product\SprykerProductApiExtendedConstants;

class VariantImagesHelper
{
    /**
     * @param array $imageSets
     *
     * @return string[][]
     */
    public static function mapImageSets(array $imageSets): array
    {
        $images = [];

        foreach ($imageSets as $imageSet) {
            $setName = $imageSet['name'] ?? SprykerProductApiExtendedConstants::DEFAULT_IMAGE_SET_NAME;

            foreach ($imageSet['images'] as $image) {
                // @TODO: Should we keep all image sizes?
                // $images[$setName][SprykerProductApiExtendedConstants::IMAGE_SIZE_LARGE][] =
                //     $image['externalUrlLarge'];
                // $images[$setName][SprykerProductApiExtendedConstants::IMAGE_SIZE_SMALL][] =
                //     $image['externalUrlSmall'];

                // if (isset($image['externalUrlMedium'])) {
                //     $images[$setName][SprykerProductApiExtendedConstants::IMAGE_SIZE_MEDIUM][] =
                //         $image['externalUrlSmall'];
                // }
                $images[] = $image['externalUrlLarge'];
            }
        }

        return $images;
    }
}
