<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model {

    protected $table = 'productos';
    protected $primaryKey = 'id';
    public $timestamps = false;

    public function imagenes() {
        return $this->hasMany(ImagenProducto::class, 'producto_id');
    }

    public function precios() {
        return $this->hasMany(Precio::class, 'producto_id')->orderBy('fecha_reg', 'desc');
    }

    public function ultimos_precios() {
        return $this->precios()->take(5);
    }

    public function precio_actual() {
        return $this->hasOne(Precio::class, 'producto_id')->where('actual', 1);
    }

    public function ofertas() {
        return $this->hasMany(Oferta::class, 'producto_id')->orderBy('fecha_reg', 'desc');
    }

    public function ultimas_ofertas() {
        return $this->ofertas()->take(5);
    }

    public function oferta_vigente() {
        $sHoy = today()->toDateString();
        return $this->hasOne(Oferta::class, 'producto_id')
            ->where('eliminado', 0)
            ->whereRaw('? between fecha_inicio and fecha_vencimiento', [$sHoy]);
    }

    /*------------------------*/
    public function promociones() {
        return $this->hasMany(Promocion::class, 'producto_id')->orderBy('fecha_reg', 'desc');
    }

    public function ultimas_promociones() {
        return $this->promociones()->take(5);
    }

    public function promocion_vigente() {
        $sHoy = today()->toDateString();
        return $this->hasOne(Promocion::class, 'producto_id')
            ->where('eliminado', 0)
            ->whereRaw('? between fecha_inicio and fecha_vencimiento', [$sHoy]);
    }
    /*------------------------*/

    public function documentos() {
        return $this->hasMany(DocumentoProducto::class, 'producto_id');
    }

    public function productos_categorias() {
        return $this->hasMany(ProductoCategoria::class, 'producto_id');
    }

    public function categorias() {
        return $this->belongsToMany(CategoriaProducto::class, 'productos_categorias', 'producto_id', 'categoria_id');
    }

    public function productos_lineas() {
        return $this->hasMany(ProductoLinea::class, 'producto_id');
    }

    public function lineas() {
        return $this->belongsToMany(LineaProducto::class, 'productos_lineas', 'producto_id', 'linea_id');
    }

    public function subproductos() {
        return $this->belongsToMany(Producto::class, 'productos_subproductos', 'producto_id', 'subproducto_id');
    }

    public function detalles() {
        return $this->hasMany(DetalleCompra::class, 'producto_id');
    }

    public function calificaciones() {
        return $this->hasMany(Calificacion::class, 'producto_id')
                ->orderBy('created_at', 'desc');
    }

    public function calificacion_5() {
        return $this->hasMany(Calificacion::class, 'producto_id')
                ->where('stars', 5);
    }

    public function calificacion_4() {
        return $this->hasMany(Calificacion::class, 'producto_id')
                ->where('stars', 4);
    }

    public function calificacion_3() {
        return $this->hasMany(Calificacion::class, 'producto_id')
                ->where('stars', 3);
    }

    public function calificacion_2() {
        return $this->hasMany(Calificacion::class, 'producto_id')
                ->where('stars', 2);
    }

    public function calificacion_1() {
        return $this->hasMany(Calificacion::class, 'producto_id')
                ->where('stars', 1);
    }

    public function cantidad_calificaciones() {
        return count($this->calificaciones);
    }

    public function sumatoria_calificaciones() {
        $calificaciones = $this->calificaciones;
        $sumatoria = 0;
        foreach($calificaciones as $item)
        {
            $sumatoria = $sumatoria + $item->stars;
        }
        return $sumatoria;
    }
}
