import 'package:flutter/material.dart';
import 'package:cached_network_image/cached_network_image.dart';
import 'package:shimmer/shimmer.dart';
import '../../services/user_api_service.dart';
import '../products/products_screen.dart';

class CategoriesScreen extends StatefulWidget {
  const CategoriesScreen({super.key});

  @override
  State<CategoriesScreen> createState() => _CategoriesScreenState();
}

class _CategoriesScreenState extends State<CategoriesScreen> {
  List<Map<String, dynamic>> _categories = [];
  bool _isLoading = true;
  int _selectedCategoryIndex = 0;
  String? _error;

  @override
  void initState() {
    super.initState();
    _loadCategories();
  }

  Future<void> _loadCategories() async {
    try {
      setState(() {
        _isLoading = true;
        _error = null;
      });

      final response = await UserApiService.getCategories();
      
      // Handle different response structures
      List<Map<String, dynamic>> categories = [];
      if (response['success'] == true && response['data'] != null) {
        final categoriesData = response['data'];
        if (categoriesData is List) {
          categories = List<Map<String, dynamic>>.from(categoriesData);
        } else if (categoriesData is Map && categoriesData['data'] is List) {
          categories = List<Map<String, dynamic>>.from(categoriesData['data']);
        }
      }

      // If API fails or returns empty, show mock data for demo
      if (categories.isEmpty) {
        categories = _getMockCategories();
      }

      setState(() {
        _categories = categories;
        _isLoading = false;
      });
    } catch (e) {
      print('Error loading categories: $e');
      
      // Use mock data as fallback
      setState(() {
        _categories = _getMockCategories();
        _isLoading = false;
        _error = e.toString();
      });
      
      // Show error message to user
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: const Text('Using demo data - Backend connection failed'),
            backgroundColor: Colors.orange,
            action: SnackBarAction(
              label: 'Retry',
              textColor: Colors.white,
              onPressed: _loadCategories,
            ),
          ),
        );
      }
    }
  }

  List<Map<String, dynamic>> _getMockCategories() {
    return [
      {
        'id': '1',
        'name': 'Women Fashion',
        'description': 'Trendy clothes for women',
        'image': 'https://via.placeholder.com/300/FF6B6B/FFFFFF?text=Women+Fashion',
        'products_count': 150,
      },
      {
        'id': '2',
        'name': 'Men Fashion',
        'description': 'Stylish clothing for men',
        'image': 'https://via.placeholder.com/300/4ECDC4/FFFFFF?text=Men+Fashion',
        'products_count': 120,
      },
      {
        'id': '3',
        'name': 'Electronics',
        'description': 'Latest gadgets and electronics',
        'image': 'https://via.placeholder.com/300/45B7D1/FFFFFF?text=Electronics',
        'products_count': 89,
      },
      {
        'id': '4',
        'name': 'Home & Kitchen',
        'description': 'Everything for your home',
        'image': 'https://via.placeholder.com/300/96CEB4/FFFFFF?text=Home+Kitchen',
        'products_count': 200,
      },
      {
        'id': '5',
        'name': 'Beauty & Health',
        'description': 'Beauty and wellness products',
        'image': 'https://via.placeholder.com/300/FFEAA7/FFFFFF?text=Beauty+Health',
        'products_count': 75,
      },
      {
        'id': '6',
        'name': 'Kids & Toys',
        'description': 'Fun and educational toys',
        'image': 'https://via.placeholder.com/300/DDA0DD/FFFFFF?text=Kids+Toys',
        'products_count': 95,
      },
    ];
  }



  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.grey[50],
      appBar: AppBar(
        title: const Text(
          'CATEGORIES',
          style: TextStyle(
            fontWeight: FontWeight.w600,
            color: Colors.black87,
            fontSize: 16,
          ),
        ),
        backgroundColor: Colors.white,
        elevation: 0,
        centerTitle: false,
        actions: [
          IconButton(
            icon: const Icon(Icons.search, color: Colors.black87),
            onPressed: () {
              // Navigate to search
            },
          ),
          IconButton(
            icon: const Icon(Icons.favorite_border, color: Colors.black87),
            onPressed: () {
              // Navigate to wishlist
            },
          ),
          Stack(
            children: [
              IconButton(
                icon: const Icon(Icons.shopping_cart_outlined, color: Colors.black87),
                onPressed: () {
                  // Navigate to cart
                },
              ),
              Positioned(
                right: 8,
                top: 8,
                child: Container(
                  padding: const EdgeInsets.all(2),
                  decoration: BoxDecoration(
                    color: Colors.red,
                    borderRadius: BorderRadius.circular(10),
                  ),
                  constraints: const BoxConstraints(
                    minWidth: 16,
                    minHeight: 16,
                  ),
                  child: const Text(
                    '0',
                    style: TextStyle(
                      color: Colors.white,
                      fontSize: 12,
                    ),
                    textAlign: TextAlign.center,
                  ),
                ),
              ),
            ],
          ),
        ],
      ),
      body: _isLoading
          ? _buildShimmerLayout()
          : Row(
              children: [
                // Left Sidebar
                Container(
                  width: 100,
                  color: Colors.grey[50],
                  child: _buildCategorySidebar(),
                ),
                // Right Content
                Expanded(
                  child: _buildCategoryContent(),
                ),
              ],
            ),
    );
  }

  Widget _buildCategorySidebar() {
    final sidebarCategories = [
      {'name': 'Popular', 'icon': Icons.star, 'isSelected': _selectedCategoryIndex == 0},
      {'name': 'Kurti, Saree\n& Lehenga', 'icon': Icons.checkroom, 'isSelected': _selectedCategoryIndex == 1},
      {'name': 'Women\nWestern', 'icon': Icons.woman, 'isSelected': _selectedCategoryIndex == 2},
      {'name': 'Lingerie', 'icon': Icons.favorite, 'isSelected': _selectedCategoryIndex == 3},
      {'name': 'Men', 'icon': Icons.man, 'isSelected': _selectedCategoryIndex == 4},
      {'name': 'Kids & Toys', 'icon': Icons.child_care, 'isSelected': _selectedCategoryIndex == 5},
      {'name': 'Home &\nKitchen', 'icon': Icons.home, 'isSelected': _selectedCategoryIndex == 6},
      {'name': 'Beauty &\nHealth', 'icon': Icons.spa, 'isSelected': _selectedCategoryIndex == 7},
    ];

    return ListView.builder(
      itemCount: sidebarCategories.length,
      itemBuilder: (context, index) {
        final category = sidebarCategories[index];
        return GestureDetector(
          onTap: () {
            setState(() {
              _selectedCategoryIndex = index;
            });
          },
          child: Container(
            padding: const EdgeInsets.symmetric(vertical: 12, horizontal: 8),
            decoration: BoxDecoration(
              color: category['isSelected'] as bool ? Colors.white : Colors.transparent,
              border: Border(
                left: BorderSide(
                  color: category['isSelected'] as bool ? Colors.pink : Colors.transparent,
                  width: 3,
                ),
              ),
            ),
            child: Column(
              children: [
                Icon(
                  category['icon'] as IconData,
                  size: 24,
                  color: category['isSelected'] as bool ? Colors.pink : Colors.grey[600],
                ),
                const SizedBox(height: 4),
                Text(
                  category['name'] as String,
                  textAlign: TextAlign.center,
                  style: TextStyle(
                    fontSize: 10,
                    fontWeight: category['isSelected'] as bool ? FontWeight.w600 : FontWeight.normal,
                    color: category['isSelected'] as bool ? Colors.pink : Colors.grey[700],
                  ),
                ),
              ],
            ),
          ),
        );
      },
    );
  }

  Widget _buildCategoryContent() {
    if (_selectedCategoryIndex == 0) {
      return _buildPopularContent();
    }
    return _buildSubcategoryGrid();
  }

  Widget _buildPopularContent() {
    return SingleChildScrollView(
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // Featured On Meesho section
          Container(
            padding: const EdgeInsets.all(16),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const Text(
                  'Featured On Meesho',
                  style: TextStyle(
                    fontSize: 16,
                    fontWeight: FontWeight.w600,
                    color: Colors.black87,
                  ),
                ),
                const SizedBox(height: 12),
                Row(
                  children: [
                    _buildFeaturedCard('Smartphones', Icons.phone_android, Colors.blue),
                    const SizedBox(width: 12),
                    _buildFeaturedCard('Top Brands', Icons.star, Colors.orange),
                    const SizedBox(width: 12),
                    _buildFeaturedCard('Premium\nCollection', Icons.diamond, Colors.purple),
                  ],
                ),
                const SizedBox(height: 16),
                Row(
                  children: [
                    _buildFeaturedCard('Fresh Apples', Icons.apple, Colors.green),
                    const SizedBox(width: 12),
                    _buildFeaturedCard('Mom & Kids\nStore', Icons.child_care, Colors.pink),
                    const SizedBox(width: 12),
                    _buildFeaturedCard('Cookware', Icons.kitchen, Colors.brown),
                  ],
                ),
              ],
            ),
          ),
          
          // All Popular section
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 16),
            child: const Text(
              'All Popular',
              style: TextStyle(
                fontSize: 16,
                fontWeight: FontWeight.w600,
                color: Colors.black87,
              ),
            ),
          ),
          const SizedBox(height: 12),
          _buildPopularProductsGrid(),
        ],
      ),
    );
  }

  Widget _buildFeaturedCard(String title, IconData icon, Color color) {
    return Expanded(
      child: Container(
        height: 80,
        decoration: BoxDecoration(
          color: color.withOpacity(0.1),
          borderRadius: BorderRadius.circular(8),
          border: Border.all(color: color.withOpacity(0.3)),
        ),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(icon, color: color, size: 24),
            const SizedBox(height: 4),
            Text(
              title,
              textAlign: TextAlign.center,
              style: TextStyle(
                fontSize: 10,
                fontWeight: FontWeight.w500,
                color: color,
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildPopularProductsGrid() {
    final popularProducts = [
      {'name': 'Kurtis & Dress\nMaterials', 'image': 'assets/images/kurti.jpg'},
      {'name': 'Sarees', 'image': 'assets/images/saree.jpg'},
      {'name': 'Westernwear', 'image': 'assets/images/western.jpg'},
      {'name': 'Jewellery', 'image': 'assets/images/jewelry.jpg'},
      {'name': 'Men Fashion', 'image': 'assets/images/men.jpg'},
      {'name': 'Kids', 'image': 'assets/images/kids.jpg'},
      {'name': 'Footwear', 'image': 'assets/images/footwear.jpg'},
      {'name': 'Beauty &\nPersonal Care', 'image': 'assets/images/beauty.jpg'},
      {'name': 'Grocery', 'image': 'assets/images/grocery.jpg'},
    ];

    return GridView.builder(
      shrinkWrap: true,
      physics: const NeverScrollableScrollPhysics(),
      padding: const EdgeInsets.symmetric(horizontal: 16),
      gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
        crossAxisCount: 3,
        childAspectRatio: 0.8,
        crossAxisSpacing: 12,
        mainAxisSpacing: 12,
      ),
      itemCount: popularProducts.length,
      itemBuilder: (context, index) {
        final product = popularProducts[index];
        return GestureDetector(
          onTap: () {
            Navigator.push(
              context,
              MaterialPageRoute(
                builder: (context) => ProductsScreen(categoryId: index.toString()),
              ),
            );
          },
          child: Container(
            decoration: BoxDecoration(
              color: Colors.white,
              borderRadius: BorderRadius.circular(8),
              boxShadow: [
                BoxShadow(
                  color: Colors.grey.withOpacity(0.1),
                  spreadRadius: 1,
                  blurRadius: 4,
                  offset: const Offset(0, 2),
                ),
              ],
            ),
            child: Column(
              children: [
                Expanded(
                  child: Container(
                    decoration: BoxDecoration(
                      color: Colors.grey[100],
                      borderRadius: const BorderRadius.vertical(top: Radius.circular(8)),
                    ),
                    child: Center(
                      child: Icon(
                        _getIconForProduct(index),
                        size: 40,
                        color: Colors.grey[600],
                      ),
                    ),
                  ),
                ),
                Padding(
                  padding: const EdgeInsets.all(8),
                  child: Text(
                    product['name'] as String,
                    textAlign: TextAlign.center,
                    style: const TextStyle(
                      fontSize: 11,
                      fontWeight: FontWeight.w500,
                      color: Colors.black87,
                    ),
                  ),
                ),
              ],
            ),
          ),
        );
      },
    );
  }

  IconData _getIconForProduct(int index) {
    final icons = [
      Icons.checkroom, Icons.woman, Icons.woman_2, Icons.diamond,
      Icons.man, Icons.child_care, Icons.directions_walk, Icons.spa, Icons.local_grocery_store
    ];
    return icons[index % icons.length];
  }

  Widget _buildSubcategoryGrid() {
    return GridView.builder(
      padding: const EdgeInsets.all(16),
      gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
        crossAxisCount: 2,
        childAspectRatio: 1.2,
        crossAxisSpacing: 12,
        mainAxisSpacing: 12,
      ),
      itemCount: _categories.length,
      itemBuilder: (context, index) {
        final category = _categories[index];
        return _buildCategoryCard(category);
      },
    );
  }

  Widget _buildShimmerLayout() {
    return Row(
      children: [
        Container(
          width: 100,
          color: Colors.grey[50],
          child: ListView.builder(
            itemCount: 8,
            itemBuilder: (context, index) {
              return Container(
                padding: const EdgeInsets.symmetric(vertical: 12, horizontal: 8),
                child: Shimmer.fromColors(
                  baseColor: Colors.grey[300]!,
                  highlightColor: Colors.grey[100]!,
                  child: Column(
                    children: [
                      Container(
                        width: 24,
                        height: 24,
                        decoration: BoxDecoration(
                          color: Colors.white,
                          borderRadius: BorderRadius.circular(4),
                        ),
                      ),
                      const SizedBox(height: 4),
                      Container(
                        width: 60,
                        height: 20,
                        decoration: BoxDecoration(
                          color: Colors.white,
                          borderRadius: BorderRadius.circular(4),
                        ),
                      ),
                    ],
                  ),
                ),
              );
            },
          ),
        ),
        Expanded(
          child: Shimmer.fromColors(
            baseColor: Colors.grey[300]!,
            highlightColor: Colors.grey[100]!,
            child: GridView.builder(
              padding: const EdgeInsets.all(16),
              gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
                crossAxisCount: 3,
                childAspectRatio: 0.8,
                crossAxisSpacing: 12,
                mainAxisSpacing: 12,
              ),
              itemCount: 9,
              itemBuilder: (context, index) {
                return Container(
                  decoration: BoxDecoration(
                    color: Colors.white,
                    borderRadius: BorderRadius.circular(8),
                  ),
                );
              },
            ),
          ),
        ),
      ],
    );
  }

  Widget _buildCategoryCard(Map<String, dynamic> category) {
    return GestureDetector(
      onTap: () {
        Navigator.of(context).push(
          MaterialPageRoute(
            builder: (context) => ProductsScreen(
              categoryId: category['id'],
              categoryName: category['name'],
            ),
          ),
        );
      },
      child: Container(
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(12),
          boxShadow: [
            BoxShadow(
              color: Colors.black.withOpacity(0.1),
              blurRadius: 8,
              offset: const Offset(0, 2),
            ),
          ],
        ),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Category Image
            Expanded(
              flex: 3,
              child: Container(
                width: double.infinity,
                decoration: const BoxDecoration(
                  borderRadius: BorderRadius.vertical(top: Radius.circular(12)),
                ),
                child: ClipRRect(
                  borderRadius: const BorderRadius.vertical(top: Radius.circular(12)),
                  child: CachedNetworkImage(
                    imageUrl: category['image'] ?? '',
                    fit: BoxFit.cover,
                    placeholder: (context, url) => Container(
                      color: Colors.grey[200],
                      child: const Center(
                        child: CircularProgressIndicator(),
                      ),
                    ),
                    errorWidget: (context, url, error) => Container(
                      color: Colors.blue[50],
                      child: Icon(
                        Icons.category,
                        color: Colors.blue,
                        size: 40,
                      ),
                    ),
                  ),
                ),
              ),
            ),
            
            // Category Details
            Expanded(
              flex: 2,
              child: Padding(
                padding: const EdgeInsets.all(12),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      category['name'] ?? '',
                      style: const TextStyle(
                        fontSize: 16,
                        fontWeight: FontWeight.bold,
                      ),
                      maxLines: 1,
                      overflow: TextOverflow.ellipsis,
                    ),
                    const SizedBox(height: 4),
                    if (category['description'] != null)
                      Text(
                        category['description'],
                        style: TextStyle(
                          fontSize: 12,
                          color: Colors.grey[600],
                        ),
                        maxLines: 2,
                        overflow: TextOverflow.ellipsis,
                      ),
                    const Spacer(),
                    if (category['products_count'] != null)
                      Container(
                        padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                        decoration: BoxDecoration(
                          color: Colors.blue[50],
                          borderRadius: BorderRadius.circular(12),
                        ),
                        child: Text(
                          '${category['products_count']} items',
                          style: TextStyle(
                            color: Colors.blue[700],
                            fontSize: 10,
                            fontWeight: FontWeight.w600,
                          ),
                        ),
                      ),
                  ],
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }
}
