import { ScrollView, Text, View, Image, Alert } from 'react-native'
import React, { useState } from 'react'
import { SafeAreaView } from 'react-native-safe-area-context'

import images from '../../constants/images'
import FormField from '../../components/FormField'
import CustomButton from '../../components/CustomButton'

import { Link, router } from 'expo-router'
import axios from "axios";
import * as SecureStore from "expo-secure-store";
import {getCurrentUser, signIn, signUp} from "../../components/API/api";
import { useGlobalContext } from "../../context/GlobalProvider";

const SignUp = () => {
    const { setUser, setIsLogged } = useGlobalContext();

    const [form, setForm] = useState({
        firstName: '',
        lastName: '',
        email: '',
        password: '',
        passwordConfirmation: '',
    })

    const [isSubmitting, setIsSubmitting] = useState(false)

    const submit = async () => {
        if(
            !form.password
            || !form.email
            || !form.firstName
            || !form.lastName
            || !form.email
            || !form.passwordConfirmation
        ){
            Alert.alert('Error', 'Please fill in all the fields')
            return;
        }

        setIsSubmitting(true);

        try {
            const responseSignUp = await signUp(
                form.firstName,
                form.lastName,
                form.email,
                form.password,
                form.passwordConfirmation
            );

            if(responseSignUp === 200) {
                const response = await signIn(form.email, form.password);

                if(response === 200) {
                    const result = await getCurrentUser();

                    if(result !== null) {
                        setUser(result);
                        setIsLogged(true);

                        // Redirect to home screen upon successful login
                        router.replace('/home');
                    }
                }
            }
        } catch (error) {
            Alert.alert('Error', error.message);
        } finally {
            setIsSubmitting(false);
        }
    }

    return (
        <SafeAreaView className="bg-primary h-full">
            <ScrollView>
                <View className="w-full justify-center min-h-[70vh] px-4 my-6">
                    <View className='items-center'>
                        <Image source={images.logo} resizeMode='contain' className="w-[190px] h-[45px]" />
                        <Text className="text-[25px] text-black mt-7 font-abold">Create EdgeTracker account</Text>
                        <Text className="text-[16px] text-black mt-3 font-asemibold">Already have an account?  <Link href="/sign-in" className='underline text-brand-primary'>Login</Link></Text>
                    </View>

                    <View className='flex-row justify-between'>
                        <FormField
                            title="First name"
                            value={form.firstName}
                            placeholder='Enter your name'
                            handleChangeText={(e) => setForm({ ...form, firstName: e})}
                            otherStyles="mt-7"
                            keyboardType="email-address"
                        />
                        <FormField
                            title="Last name"
                            value={form.lastName}
                            placeholder='Enter your name'
                            handleChangeText={(e) => setForm({ ...form, lastName: e})}
                            otherStyles="mt-7"
                            keyboardType="email-address"
                        />
                    </View>
                    <FormField
                        title="Email"
                        value={form.email}
                        placeholder='Enter your email'
                        handleChangeText={(e) => setForm({ ...form, email: e})}
                        otherStyles="mt-3"
                        keyboardType="email-address"
                    />
                    <FormField
                        title="Password"
                        placeholder='Enter your password'
                        value={form.password}
                        handleChangeText={(e) => setForm({ ...form, password: e})}
                        otherStyles="mt-3"
                    />
                    <FormField
                        title="Confirm Password"
                        placeholder='Confirm your password'
                        value={form.passwordConfirmation}
                        handleChangeText={(e) => setForm({ ...form, passwordConfirmation: e})}
                        otherStyles="mt-3"
                    />

                    <CustomButton title="Register" handlePress={submit} containerStyles="mt-8" isLoading={isSubmitting} />
                </View>
            </ScrollView>
        </SafeAreaView>
    )
}

export default SignUp