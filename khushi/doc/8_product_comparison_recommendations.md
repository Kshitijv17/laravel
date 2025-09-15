# Product Comparison & Recommendations System

## Overview
The Product Comparison & Recommendations system provides advanced functionality for users to compare products side-by-side and receive personalized product recommendations based on various algorithms.

## Features

### Product Comparison
- **Side-by-side comparison** of up to 4 products
- **Detailed comparison table** with specifications, pricing, and features
- **Export functionality** (PDF, CSV formats)
- **Share comparisons** with generated shareable links
- **Similar alternatives** suggestions
- **Session-based** comparison for guest users
- **User-based** comparison for authenticated users

### Product Recommendations
- **Collaborative filtering** - "Users who bought this also bought"
- **Content-based filtering** - Similar products by category, brand, price
- **Trending products** - Based on recent sales data
- **Cross-sell recommendations** - Complementary products
- **Personalized recommendations** - Based on user behavior
- **Recently viewed** - Recommendations from browsing history
- **Abandoned cart** - Recommendations for cart items
- **Frequently bought together** - Bundle recommendations

## Database Schema

### ProductComparison Model
```php
- id: Primary key
- user_id: Foreign key to users (nullable for guests)
- session_id: Session identifier for guest users
- product_ids: JSON array of product IDs
- comparison_data: JSON metadata about comparison
- timestamps
```

### ProductRecommendation Model
```php
- id: Primary key
- user_id: Foreign key to users (nullable)
- product_id: Source product ID
- recommended_product_id: Recommended product ID
- recommendation_type: Type of recommendation
- score: Recommendation confidence score (0.0-1.0)
- reason: Human-readable reason
- metadata: JSON additional data
- timestamps
```

## Services

### ComparisonService
**Location:** `app/Services/ComparisonService.php`

**Key Methods:**
- `addToComparison($productId, $userId)` - Add product to comparison
- `removeFromComparison($productId, $userId)` - Remove product from comparison
- `getComparisonData($userId, $sessionId)` - Get formatted comparison data
- `clearComparison($userId, $sessionId)` - Clear all products from comparison
- `generateComparisonTable($products)` - Generate detailed comparison table
- `getSimilarAlternatives($productIds, $limit)` - Get alternative products

**Features:**
- Maximum 4 products per comparison
- Automatic comparison table generation
- Similar alternatives based on categories and brands
- Export data formatting

### RecommendationService
**Location:** `app/Services/RecommendationService.php`

**Key Methods:**
- `getRecommendations($productId, $userId, $limit)` - Get mixed recommendations
- `getPersonalizedRecommendations($userId, $limit)` - User-specific recommendations
- `getSimilarProducts($productId, $limit)` - Content-based similarity
- `getFrequentlyBoughtTogether($productId, $limit)` - Bundle recommendations
- `getRecentlyViewedRecommendations($userId, $limit)` - Based on browsing history
- `getAbandonedCartRecommendations($userId, $limit)` - Cart-based recommendations

**Algorithms:**
- **Collaborative Filtering:** Analyzes user purchase patterns
- **Content-Based:** Uses product attributes (category, brand, price)
- **Trending Analysis:** Recent sales performance
- **Cross-Sell:** Complementary category mapping
- **Hybrid Scoring:** Combines multiple recommendation types

## Controllers

### ComparisonController
**Location:** `app/Http/Controllers/Web/ComparisonController.php`

**Routes:**
- `GET /comparison` - Comparison page
- `POST /comparison/add` - Add product to comparison
- `POST /comparison/remove` - Remove product from comparison
- `POST /comparison/clear` - Clear all products
- `GET /comparison/export` - Export comparison data
- `POST /comparison/share` - Generate shareable link
- `GET /comparison/shared/{code}` - View shared comparison

### RecommendationController
**Location:** `app/Http/Controllers/Web/RecommendationController.php`

**Routes:**
- `GET /recommendations` - Recommendations dashboard
- `GET /recommendations/product/{id}` - Product-based recommendations
- `GET /recommendations/personalized` - User personalized recommendations
- `GET /recommendations/similar/{id}` - Similar products
- `GET /recommendations/frequently-bought/{id}` - Bundle recommendations
- `POST /recommendations/track-click` - Track recommendation clicks

## Views

### Comparison Views
- `resources/views/web/comparison/index.blade.php` - Main comparison page
- `resources/views/web/comparison/widget.blade.php` - Comparison widget
- `resources/views/web/comparison/shared.blade.php` - Shared comparison view

### Recommendation Views
- `resources/views/web/recommendations/widget.blade.php` - Recommendation widget
- `resources/views/web/recommendations/dashboard.blade.php` - User recommendations dashboard

## Frontend Integration

### JavaScript Functions
```javascript
// Comparison
addToComparison(productId)
removeFromComparison(productId)
clearComparison()
exportComparison(format)
shareComparison()

// Recommendations
trackRecommendationClick(productId, type)
loadRecommendations(type, productId)
```

### Widget Integration
```php
// Comparison Widget
@include('web.comparison.widget', ['comparisonData' => $comparisonData])

// Recommendation Widget
@include('web.recommendations.widget', [
    'recommendations' => $recommendations,
    'type' => 'personalized'
])
```

## Analytics Integration

### Tracked Events
- `recommendation_view` - When recommendations are displayed
- `recommendation_click` - When user clicks on recommended product
- `personalized_recommendation_view` - Personalized recommendations shown
- `comparison_view` - Comparison page viewed
- `comparison_export` - Comparison exported
- `comparison_share` - Comparison shared

### Analytics Data
```php
[
    'product_id' => $productId,
    'recommended_product_id' => $recommendedProductId,
    'recommendation_type' => $type,
    'recommendation_count' => $count,
    'position' => $position
]
```

## Caching Strategy

### Recommendation Caching
- **Product recommendations:** 1 hour TTL
- **Personalized recommendations:** 30 minutes TTL
- **Similar products:** 1 hour TTL
- **Frequently bought together:** 1 hour TTL

### Cache Keys
```php
"recommendations:{$productId}:{$userId}:{$limit}"
"personalized_recommendations:{$userId}:{$limit}"
"similar_products:{$productId}:{$limit}"
"frequently_bought_together:{$productId}:{$limit}"
```

### Cache Invalidation
- Product updates clear related recommendation caches
- User purchase events clear personalized caches
- Manual cache clearing available through service methods

## Performance Considerations

### Database Optimization
- Indexes on frequently queried fields
- Efficient JOIN queries for recommendations
- Pagination for large result sets
- Query result caching

### Recommendation Algorithms
- Batch processing for collaborative filtering
- Pre-computed similarity scores
- Incremental updates for trending products
- Background job processing for heavy computations

## Security Features

### Data Protection
- User-based access control for comparisons
- Session-based comparison for guests
- CSRF protection on all POST routes
- Input validation and sanitization

### Privacy
- Anonymous comparison tracking
- Optional user association
- Automatic cleanup of old comparison data
- Secure sharing with time-limited links

## Configuration

### Recommendation Settings
```php
// config/recommendations.php
'max_comparison_products' => 4,
'cache_ttl' => [
    'recommendations' => 3600,
    'personalized' => 1800,
    'similar' => 3600,
],
'algorithms' => [
    'collaborative_weight' => 0.3,
    'content_based_weight' => 0.4,
    'trending_weight' => 0.2,
    'cross_sell_weight' => 0.1,
]
```

## API Endpoints

### REST API
All comparison and recommendation endpoints support JSON responses when requested with `Accept: application/json` header or `ajax` requests.

### Response Format
```json
{
    "success": true,
    "data": {
        "recommendations": [...],
        "count": 8,
        "type": "personalized"
    },
    "message": "Success"
}
```

## Usage Examples

### Adding Product to Comparison
```javascript
fetch('/comparison/add', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken
    },
    body: JSON.stringify({ product_id: 123 })
})
```

### Getting Personalized Recommendations
```javascript
fetch('/recommendations/personalized?limit=8')
    .then(response => response.json())
    .then(data => {
        // Display recommendations
    });
```

### Tracking Recommendation Clicks
```javascript
fetch('/recommendations/track-click', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken
    },
    body: JSON.stringify({
        product_id: 123,
        recommended_product_id: 456,
        recommendation_type: 'personalized',
        position: 1
    })
})
```

## Testing

### Unit Tests
- Service method testing
- Algorithm accuracy testing
- Cache functionality testing

### Integration Tests
- Controller endpoint testing
- Database interaction testing
- Analytics tracking testing

### Performance Tests
- Recommendation generation speed
- Cache hit/miss ratios
- Database query optimization

## Maintenance

### Regular Tasks
- Clean up old comparison data (30+ days)
- Update recommendation algorithms based on performance
- Monitor cache hit ratios
- Analyze recommendation click-through rates

### Monitoring
- Track recommendation accuracy
- Monitor system performance impact
- Analyze user engagement with recommendations
- Review comparison usage patterns

## Future Enhancements

### Advanced Features
- Machine learning-based recommendations
- Real-time recommendation updates
- A/B testing for recommendation algorithms
- Advanced filtering and sorting options

### Integration Opportunities
- Email recommendation campaigns
- Push notification recommendations
- Social media sharing integration
- Third-party recommendation services
