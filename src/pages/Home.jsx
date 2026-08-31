import { useEffect, useState } from 'react';
import ProductCard from '../components/ProductCard.jsx';
import { PRODUCTS, fetchProducts } from '../products.js';

export default function Home() {
  const [products, setProducts] = useState(PRODUCTS);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    fetchProducts()
      .then((data) => setProducts(data))
      .catch(() => setError('Could not reach the product API — showing local data.'))
      .finally(() => setLoading(false));
  }, []);

  return (
    <>
      <section className="mb-8">
        <h1 className="text-3xl font-bold tracking-tight mb-2">
          Everyday essentials, thoughtfully picked.
        </h1>
        <p className="text-gray-500">Browse our small, curated collection.</p>
        {error && (
          <p className="mt-2 text-sm text-amber-600 bg-amber-50 border border-amber-200 rounded-lg px-3 py-2">
            {error}
          </p>
        )}
      </section>
      {loading ? (
        <p className="text-gray-400">Loading…</p>
      ) : (
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
          {products.map((p) => (
            <ProductCard key={p.id} product={p} />
          ))}
        </div>
      )}
    </>
  );
}
