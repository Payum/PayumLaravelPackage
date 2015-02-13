<?php
namespace Payum\LaravelPackage\Storage;

use Illuminate\Database\Eloquent\Model;
use Payum\Core\Model\Identity;
use Payum\Core\Storage\AbstractStorage;

class EloquentStorage extends AbstractStorage
{
    /**
     * {@inheritDoc}
     *
     * @param Model $model
     */
    protected function doUpdateModel($model)
    {
        $model->save();
    }

    /**
     * {@inheritDoc}
     *
     * @param Model $model
     */
    protected function doDeleteModel($model)
    {
        $model->delete();
    }

    /**
     * {@inheritDoc}
     *
     * @param Model $model
     */
    protected function doGetIdentity($model)
    {
        return new Identity($model->id, $model);
    }

    /**
     * {@inheritDoc}
     *
     * @return Model|null
     */
    protected function doFind($id)
    {
        $modelClass = $this->modelClass;

        return $modelClass::find($id);
    }

    /**
     * {@inheritDoc}
     *
     * @return Model|null
     */
    public function findBy(array $criteria)
    {
        $modelClass = $this->modelClass;

        return $modelClass::all($criteria);
    }
}