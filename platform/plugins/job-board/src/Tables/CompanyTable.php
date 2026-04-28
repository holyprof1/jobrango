<?php

namespace Botble\JobBoard\Tables;

use Botble\Base\Facades\BaseHelper;
use Botble\JobBoard\Models\Company;
use Botble\Table\Abstracts\TableAbstract;
use Botble\Table\Actions\Action;
use Botble\Table\Actions\DeleteAction;
use Botble\Table\Actions\EditAction;
use Botble\Table\BulkActions\DeleteBulkAction;
use Botble\Table\BulkChanges\CreatedAtBulkChange;
use Botble\Table\BulkChanges\NameBulkChange;
use Botble\Table\BulkChanges\StatusBulkChange;
use Botble\Table\Columns\CreatedAtColumn;
use Botble\Table\Columns\FormattedColumn;
use Botble\Table\Columns\IdColumn;
use Botble\Table\Columns\ImageColumn;
use Botble\Table\Columns\NameColumn;
use Botble\Table\Columns\StatusColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\Relations\Relation as EloquentRelation;
use Illuminate\Database\Query\Builder as QueryBuilder;

class CompanyTable extends TableAbstract
{
    public function setup(): void
    {
        $this
            ->model(Company::class)
            ->displayActionsAsDropdown(false)
            ->addActions([
                Action::make('view')
                    ->route('companies.view')
                    ->label(trans('core/base::tables.view'))
                    ->icon('ti ti-eye')
                    ->color('primary'),
                Action::make('analytics')
                    ->route('companies.analytics')
                    ->label(trans('plugins/job-board::job.analytics.title'))
                    ->icon('ti ti-chart-line')
                    ->color('info'),
                EditAction::make()->route('companies.edit'),
                DeleteAction::make()->route('companies.destroy'),
            ]);
    }

    public function query(): Relation|Builder|QueryBuilder
    {
        $query = $this
            ->getModel()
            ->query()
            ->select([
                'id',
                'logo',
                'name',
                'is_featured',
                'is_verified',
                'created_at',
                'status',
            ]);

        return $this->applyScopes($query);
    }

    public function columns(): array
    {
        return [
            IdColumn::make(),
            ImageColumn::make('logo')
                ->title(trans('plugins/job-board::messages.logo')),
            NameColumn::make()->route('companies.edit'),
            FormattedColumn::make('display_id')
                ->getValueUsing(function (FormattedColumn $column) {
                    $item = $column->getItem();

                    return BaseHelper::clean($item->display_id ?: '&mdash;');
                })
                ->title(__('Company ID'))
                ->alignLeft(),
            FormattedColumn::make('is_verified')
                ->title(__('Verified'))
                ->getValueUsing(function (FormattedColumn $column) {
                    $item = $column->getItem();

                    return view('plugins/job-board::companies.partials.table-toggle', [
                        'url' => route('companies.toggle-verification', $item->id),
                        'inputName' => 'is_verified',
                        'checked' => (bool) $item->is_verified,
                        'label' => $item->is_verified ? __('On') : __('Off'),
                    ])->render();
                })
                ->alignCenter(),
            FormattedColumn::make('is_featured')
                ->title(__('Homepage'))
                ->getValueUsing(function (FormattedColumn $column) {
                    $item = $column->getItem();

                    return view('plugins/job-board::companies.partials.table-toggle', [
                        'url' => route('companies.toggle-homepage', $item->id),
                        'inputName' => 'is_featured',
                        'checked' => (bool) $item->is_featured,
                        'label' => $item->is_featured ? __('On') : __('Off'),
                    ])->render();
                })
                ->alignCenter(),
            CreatedAtColumn::make(),
            StatusColumn::make(),
        ];
    }

    public function buttons(): array
    {
        $buttons = $this->addCreateButton(route('companies.create'), 'companies.create');

        if ($this->hasPermission('companies.import')) {
            $buttons['import'] = [
                'link' => route('tools.data-synchronize.import.companies.index'),
                'text' => BaseHelper::renderIcon('ti ti-upload')
                    . trans('plugins/job-board::import.company.name'),
            ];
        }

        if ($this->hasPermission('companies.export')) {
            $buttons['export'] = [
                'link' => route('tools.data-synchronize.export.companies.index'),
                'text' => BaseHelper::renderIcon('ti ti-download')
                    . trans('plugins/job-board::export.companies.name'),
            ];
        }

        return $buttons;
    }

    public function bulkActions(): array
    {
        return [
            DeleteBulkAction::make()->permission('companies.destroy'),
        ];
    }

    public function getBulkChanges(): array
    {
        return [
            NameBulkChange::make(),
            StatusBulkChange::make(),
            CreatedAtBulkChange::make(),
            'is_completed_profile' => [
                'title' => trans('plugins/job-board::messages.is_completed_profile'),
                'type' => 'select',
                'choices' => [
                    'completed' => trans('plugins/job-board::messages.yes'),
                    'incomplete' => trans('plugins/job-board::messages.no'),
                ],
            ],
        ];
    }

    public function getOperationsHeading(): array
    {
        return [
            'operations' => [
                'title' => trans('core/base::tables.operations'),
                'width' => '220px',
                'class' => 'text-center',
                'orderable' => false,
                'searchable' => false,
                'exportable' => false,
                'printable' => false,
            ],
        ];
    }

    public function applyFilterCondition(
        EloquentBuilder|QueryBuilder|EloquentRelation $query,
        string $key,
        string $operator,
        ?string $value
    ): EloquentRelation|EloquentBuilder|QueryBuilder {
        if ($key == 'is_completed_profile') {
            switch ($value) {
                case 'completed':
                    // @phpstan-ignore-next-line
                    return $query->completedProfile();
                case 'incomplete':
                    // @phpstan-ignore-next-line
                    return $query->incompleteProfile();
            }
        }

        return parent::applyFilterCondition($query, $key, $operator, $value);
    }
}
