import { ScrollView, Text, View, Image, Alert } from 'react-native'
import React, { useState } from 'react'
import { SafeAreaView } from 'react-native-safe-area-context'

import images from '../../constants/images'
import FormField from '../../components/FormField'
import CustomButton from '../../components/CustomButton'

import { Link, router } from 'expo-router'
import {getCurrentUser, signIn} from "../../components/API/api";
import { useGlobalContext } from "../../context/GlobalProvider";

const SignIn = () => {
    const { setUser, setIsLogged } = useGlobalContext();

    const [form, setForm] = useState({
        email: '',
        password: ''
    })

    const [isSubmitting, setIsSubmitting] = useState(false)

    const submit = async () => {
        if(!form.password || !form.email){
            Alert.alert('Error', 'Please fill in all the fields')
            return;
        }

        setIsSubmitting(true);

        try {
            const response = await signIn(form.email, form.password);

            if(response === 200) {
                const result = await getCurrentUser();

                setUser(result);
                setIsLogged(true);

                // Redirect to home screen upon successful login
                router.replace('/home');
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
                <View className="flex-1 justify-between px-4">
                {/*<View className="w-full justify-center min-h-[70vh] px-4 my-6">*/}
                    <View className='items-center'>
                        <Text className="text-[30px] text-black mt-7 font-abold">Login</Text>
                    </View>

                    <FormField
                        // title="Email"
                        value={form.email}
                        placeholder='Email'
                        handleChangeText={(e) => setForm({ ...form, email: e})}
                        otherStyles="mt-16"
                        keyboardType="email-address"
                    />
                    <FormField
                        // title="Password"
                        placeholder='Password'
                        value={form.password}
                        handleChangeText={(e) => setForm({ ...form, password: e})}
                        otherStyles="mt-5"
                    />

                    <CustomButton title="Login" handlePress={submit} containerStyles="mt-12 bg-Violet/Violet100"  textStyles="text-Base/Light/Light80" isLoading={isSubmitting} />

                    <View className="justify-center pt-5 flex-row gap-2">
                        <Link href="/forgotPassword" className='text-[19px] mt-4 font-asemibold text-Violet/Violet100'>Forgot Password?</Link>
                    </View>
                    <View className="justify-center pt-5 flex-row gap-2">
                        <Text className='text-[18px] mt-4 font-amedium text-Base/Light/Light20'>Don’t have an account yet? <Link href="/sign-up" className='text-Violet/Violet100 underline'>Sign Up</Link></Text>
                    </View>
                    <View className="justify-end pt-5 flex-row gap-2">
                        <Link href="/home" className='text-[16px] mt-2 font-asemibold underline text-brand-primary'>test?</Link>
                    </View>
                </View>
            </ScrollView>
        </SafeAreaView>
    )
}

export default SignIn