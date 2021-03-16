<?php
/**
 * NOTICE OF LICENSE.
 *
 * UNIT3D Community Edition is open-sourced software licensed under the GNU Affero General Public License v3.0
 * The details is bundled with this project in the file LICENSE.txt.
 *
 * @project    UNIT3D Community Edition
 *
 * @author     HDVinnie <hdinnovations@protonmail.com>
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 */

namespace App\Http\Livewire;

use App\Models\Network;
use Livewire\Component;
use Livewire\WithPagination;

class NetworkSearch extends Component
{
    use WithPagination;

    public $search;

    final public function paginationView(): string
    {
        return 'vendor.pagination.livewire-pagination';
    }

    final public function updatingSearch(): void
    {
        $this->resetPage();
    }

    final public function getNetworksProperty(): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return Network::withCount('tv')
            ->where('name', 'LIKE', '%'.$this->search.'%')
            ->orderBy('name', 'asc')
            ->paginate(30);
    }

    final public function render(): \Illuminate\Contracts\View\Factory | \Illuminate\Contracts\View\View | \Illuminate\Contracts\Foundation\Application
    {
        return \view('livewire.network-search', [
            'networks' => $this->networks,
        ]);
    }
}
