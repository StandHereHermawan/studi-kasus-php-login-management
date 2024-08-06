<?php

namespace AriefKarditya\LocalDomainPhp\Controller;

class ProductController
{
    function categories(string $productId, string $categoryId): void
    {
        echo "PRODUCT-ID: $productId, CATEGORY $categoryId";
    }
}
