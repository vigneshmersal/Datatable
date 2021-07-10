## fix circular relationship on relations 
```php
class Product extends Model {
    public function url() {
        return URL::route('product', ['category' => $this->category->slug]);
    }
}
// issue loading same category
$category->load('products.category');
// fix by Manually assign the product categories.
$category->products->each->setRelation('category', $category);
```

```php
//reset relations on EXISTING MODEL (control which ones will be loaded)
$this->relations = [];

//re-sync everything
$model->getRelations()
foreach ($this->relations as $relationName => $values){
    $new->{$relationName}()->sync($values);
}
```
