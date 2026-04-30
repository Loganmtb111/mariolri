<?php

namespace App\Http\Controllers;

use App\Services\ToadCustomerService;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    private ToadCustomerService $customerService;

    public function __construct(ToadCustomerService $customerService)
    {
        $this->middleware('auth');
        $this->customerService = $customerService;
    }

    public function index()
    {
        $customers = $this->customerService->getAllCustomers() ?? [];

        return view('customers.index', ['customers' => $customers]);
    }

    public function edit($id)
    {
        $customer = $this->customerService->getCustomerById((int) $id);

        if (!$customer) {
            abort(404, 'Client non trouvé');
        }

        return view('customers.edit', ['customer' => $customer]);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'firstName' => 'required|string|max:255',
            'lastName'  => 'required|string|max:255',
            'email'     => 'required|email|max:255',
            'storeId'   => 'required|in:1,2',
            'active'    => 'required|in:0,1',
        ]);

        // Récupère le customer complet pour ne pas perdre les champs non affichés
        $current = $this->customerService->getCustomerById((int) $id) ?? [];

        // Fusionne : on écrase seulement les champs du formulaire
        $data = array_merge($current, [
            'firstName' => $validated['firstName'],
            'lastName'  => $validated['lastName'],
            'email'     => $validated['email'],
            'storeId'   => (int) $validated['storeId'],
            'active'    => (int) $validated['active'],
        ]);

        $success = $this->customerService->updateCustomer((int) $id, $data);

        if ($success) {
            return redirect()->route('customers.index')
                ->with('success', 'Client modifié avec succès.');
        }

        return back()
            ->with('error', 'Une erreur est survenue lors de la modification.')
            ->withInput();
    }

    public function destroy($id)
    {
        $success = $this->customerService->deleteCustomer((int) $id);

        if ($success) {
            return redirect()->route('customers.index')
                ->with('success', 'Client supprimé avec succès.');
        }

        return redirect()->route('customers.index')
            ->with('error', 'Impossible de supprimer ce client (il a peut-être des réservations liées).');
    }
}
