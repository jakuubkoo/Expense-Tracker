
import { Dialog, DialogBackdrop, DialogPanel, DialogTitle } from '@headlessui/react'
import 'flowbite';
import {useState} from "react";
import axios from "axios";

export default function AddExpenseModal({ isOpen, refresh, onClose }) {

    const [title, setTitle] = useState("");
    const [amount, setAmount] = useState(0.0);
    const [date, setDate] = useState("");
    const [description, setDescription] = useState("");
    const [category, setCategory] = useState("");

    const [error, setError] = useState(null);

    const loginToken = localStorage.getItem('jwtToken');

    const handleAddExpense = async () => {
        // Check if the email or password is empty
        if (!title
            || !amount
            || !date
            || !description
            || !category
        ) {
            setError('All inputs are required.');
            return;
        }

        try {
            const response = await axios.post('http://localhost:8000/api/addExpense', {
                title: title,
                amount: amount,
                date: date,
                description: description,
                category: category
            }, {
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${loginToken}`,
                },
                withCredentials: true // Send cookies if needed
            });

        } catch (err) {
            console.error(err);
            setError('Invalid email or password. Please try again.');
        }finally {
            refresh()
            onClose()
        }
    }

    if (!isOpen) return null;
    return (
        <Dialog open={isOpen} onClose={onClose} className="relative z-10">
            <DialogBackdrop
                transition
                className="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity data-[closed]:opacity-0 data-[enter]:duration-300 data-[leave]:duration-200 data-[enter]:ease-out data-[leave]:ease-in"
            />

            <div className="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div className="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <DialogPanel
                        transition
                        className="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all data-[closed]:translate-y-4 data-[closed]:opacity-0 data-[enter]:duration-300 data-[leave]:duration-200 data-[enter]:ease-out data-[leave]:ease-in sm:my-8 sm:w-full sm:max-w-lg data-[closed]:sm:translate-y-0 data-[closed]:sm:scale-95"
                    >
                        <div className="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                            <div className="sm:flex sm:items-start">
                                <div className="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                                    <DialogTitle as="h3" className="text-3xl font-semibold leading-6 text-gray-900">
                                        Add Expense
                                    </DialogTitle>
                                    {error && <p className="text-red-500 my-4">{error}</p>} {/* Display error if any */}
                                    <div className={` flex flex-col space-y-4 ${error ? 'mb-4' : 'my-4'}`}>
                                        <div className="mt-4 flex flex-row space-x-4 relative">
                                            <div className="relative">
                                                <input type="text" id="title"
                                                       onChange={e => setTitle(e.target.value)}
                                                       value={title}
                                                       className="block px-2.5 pb-2.5 pt-4 w-full text-sm text-gray-900 bg-transparent rounded-lg border-1 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                                                       placeholder=""/>
                                                <label htmlFor="title"
                                                       className="absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white dark:bg-gray-900 px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto start-1">
                                                    Title
                                                </label>
                                            </div>
                                            <div className="relative">
                                                <input type="text" id="floating_outlined"
                                                       onChange={e => setDescription(e.target.value)}
                                                       value={description}
                                                       className="block px-2.5 pb-2.5 pt-4 w-full text-sm text-gray-900 bg-transparent rounded-lg border-1 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                                                       placeholder=""/>
                                                <label htmlFor="floating_outlined"
                                                       className="absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white dark:bg-gray-900 px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto start-1">
                                                    Description
                                                </label>
                                            </div>
                                        </div>
                                        <div className="mt-4 flex flex-row space-x-4">
                                            <div className="relative">
                                                <input type="number" id="amount"
                                                       onChange={(e) => {
                                                           let val = parseInt(e.target.value, 10);
                                                           if (isNaN(val)) {
                                                               setAmount(0)
                                                           } else {
                                                               // is A Number
                                                               val = val >= 0 ? val : 0;
                                                               setAmount(val)
                                                           }
                                                       }}
                                                       value={amount}
                                                       className="block px-2.5 pb-2.5 pt-4 w-full text-sm text-gray-900 bg-transparent rounded-lg border-1 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                                                       placeholder=""/>
                                                <label htmlFor="amount"
                                                       className="absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white dark:bg-gray-900 px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto start-1">
                                                    Amount
                                                </label>
                                            </div>
                                            <div className="relative">
                                                <input type="text" id="category"
                                                       onChange={e => setCategory(e.target.value)}
                                                       value={category}
                                                       className="block px-2.5 pb-2.5 pt-4 w-full text-sm text-gray-900 bg-transparent rounded-lg border-1 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                                                       placeholder=""/>
                                                <label htmlFor="category"
                                                       className="absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white dark:bg-gray-900 px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto start-1">
                                                    Category
                                                </label>
                                            </div>
                                        </div>
                                        <div className="relative">
                                            <input type="date" id="amount"
                                                   onChange={e => setDate(e.target.value)}
                                                   value={date}
                                                   className="block px-2.5 pb-2.5 pt-4 w-full text-sm text-gray-900 bg-transparent rounded-lg border-1 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                                                   placeholder=""/>
                                            <label htmlFor="amount"
                                                   className="absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white dark:bg-gray-900 px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto start-1">
                                                Date
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div className="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                            <button
                                type="button"
                                onClick={() => handleAddExpense()}
                                className="inline-flex w-full justify-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 sm:ml-3 sm:w-auto ease-in-out duration-300"
                            >
                                Deactivate
                            </button>
                            <button
                                type="button"
                                data-autofocus
                                onClick={onClose}
                                className="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto ease-in-out duration-300"
                            >
                                Cancel
                            </button>
                        </div>
                    </DialogPanel>
                </div>
            </div>
        </Dialog>
    )
}
