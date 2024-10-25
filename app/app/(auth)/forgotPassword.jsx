import { ScrollView, Text, View, Image, Alert } from 'react-native'
import React, { useState } from 'react'
import { SafeAreaView } from 'react-native-safe-area-context'

import images from '../../constants/images'
import FormField from '../../components/FormField'
import CustomButton from '../../components/CustomButton'

import { Link, router } from 'expo-router'

const forgotPassword = () => {
    // const { setUser, setIsLogged } = useGlobalContext();

    const [form, setForm] = useState({
        email: '',
        password: ''
    })

    const [isSubmitting, setIsSubmitting] = useState(false)

    const submit = async () => {
        // if(!form.password || !form.email){
        //     Alert.alert('Error', 'Please fill in all the fields')
        // }
        //
        // setIsSubmitting(true);
        //
        // try {
        //     await signIn(form.email, form.password);
        //     const result = await getCurrentUser();
        //     setUser(result);
        //     setIsLogged(true);
        //
        //     router.replace('/home')
        // } catch (error) {
        //     Alert.alert('Error', error.message)
        // }finally{
        //     setIsSubmitting(false)
        // }
    }

    return (
        <SafeAreaView className="bg-primary h-full">
            <ScrollView>
                <View className="w-full justify-center min-h-[70vh] px-4 my-6">
                    <View className='items-center'>
                        <Image source={images.logo} resizeMode='contain' className="w-[190px] h-[45px]" />
                        <Text className="text-[30px] text-black mt-7 font-abold">Forgot Password</Text>
                        <Text className="text-[16px] text-black mt-3 font-amedium text-center">Enter the email to reset your password.{'\n'} Reset instruction will be send to your email.</Text>
                    </View>

                    <FormField
                        title="Email"
                        value={form.email}
                        placeholder='Enter your email'
                        handleChangeText={(e) => setForm({ ...form, email: e})}
                        otherStyles="mt-12"
                        keyboardType="email-address"
                    />

                    <CustomButton title="Send Instruction" handlePress={submit} containerStyles="mt-12" isLoading={isSubmitting} />
                </View>
            </ScrollView>
        </SafeAreaView>
    )
}

export default forgotPassword